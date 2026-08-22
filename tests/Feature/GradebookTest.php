<?php

namespace Tests\Feature;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Actions\Gradebook\PublishResult;
use App\Actions\Gradebook\RecordGrade;
use App\Enums\AuditAction;
use App\Enums\GradeAggregation;
use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\GradingScale;
use App\Models\GradingScaleOption;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Services\Gradebook\GradebookCalculator;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * The gradebook grades whatever the subject needs, and says what it means.
 */
class GradebookTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private ?AcademicCycleSection $cycleSection = null;

    private ?CourseOffering $courseOffering = null;

    public function test_a_mark_is_written_once_per_student_and_item(): void
    {
        $this->authorized_user([]);
        $item = $this->item(['max_points' => 20]);
        $enrollment = $this->enrollment();

        $action = app(RecordGrade::class);
        $action->record($item, $enrollment, points: 12);
        $action->record($item, $enrollment, points: 15);

        $this->assertSame(1, GradeEntry::where('grade_item_id', $item->id)->count());
        $this->assertSame(15.0, GradeEntry::firstOrFail()->points);
    }

    public function test_a_mark_above_the_maximum_is_refused(): void
    {
        $this->authorized_user([]);

        $this->expectException(InvalidValueException::class);

        app(RecordGrade::class)->record($this->item(['max_points' => 20]), $this->enrollment(), points: 21);
    }

    public function test_a_graded_entry_needs_a_number(): void
    {
        $this->authorized_user([]);

        $this->expectException(InvalidValueException::class);

        app(RecordGrade::class)->record($this->item(['max_points' => 20]), $this->enrollment());
    }

    public function test_a_state_can_be_recorded_without_a_mark(): void
    {
        $this->authorized_user([]);

        $entry = app(RecordGrade::class)->record($this->item(['max_points' => 20]), $this->enrollment(), GradeEntryState::Absent);

        $this->assertSame(GradeEntryState::Absent, $entry->state);
        $this->assertNull($entry->points);
    }

    public function test_a_closed_period_refuses_a_mark(): void
    {
        $this->authorized_user([]);
        $item = $this->item(['max_points' => 20]);
        app(ChangeAcademicPeriodStatus::class)->close($item->courseOffering->academicYear);

        $this->expectException(ClosedPeriodException::class);

        app(RecordGrade::class)->record($item->fresh(), $this->enrollment(), points: 10);
    }

    public function test_the_result_is_a_share_of_the_maximum(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        $first = $this->item(['max_points' => 20], $courseOffering);
        $second = $this->item(['max_points' => 50], $courseOffering);

        $action = app(RecordGrade::class);
        $action->record($first, $enrollment, points: 10);
        $action->record($second, $enrollment, points: 25);

        $result = app(GradebookCalculator::class)->calculate($courseOffering, $enrollment);

        $this->assertSame(50.0, $result['percentage']);
    }

    public function test_excused_work_leaves_the_total_alone(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        $marked = $this->item(['max_points' => 20], $courseOffering);
        $excused = $this->item(['max_points' => 100], $courseOffering);

        $action = app(RecordGrade::class);
        $action->record($marked, $enrollment, points: 20);
        $action->record($excused, $enrollment, GradeEntryState::Exempt);

        $result = app(GradebookCalculator::class)->calculate($courseOffering, $enrollment);

        $this->assertSame(100.0, $result['percentage']);
    }

    public function test_missing_work_counts_as_nothing(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        $marked = $this->item(['max_points' => 10], $courseOffering);
        $missed = $this->item(['max_points' => 10], $courseOffering);

        $action = app(RecordGrade::class);
        $action->record($marked, $enrollment, points: 10);
        $action->record($missed, $enrollment, GradeEntryState::Missing);

        $result = app(GradebookCalculator::class)->calculate($courseOffering, $enrollment);

        $this->assertSame(50.0, $result['percentage']);
    }

    public function test_a_comment_item_does_not_change_the_total(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        $marked = $this->item(['max_points' => 10], $courseOffering);
        $note = $this->item(['type' => GradeItemType::Text->value, 'max_points' => null], $courseOffering);

        $action = app(RecordGrade::class);
        $action->record($marked, $enrollment, points: 7);
        $action->record($note, $enrollment, comment: 'Reads with confidence.');

        $this->assertSame(70.0, app(GradebookCalculator::class)->calculate($courseOffering, $enrollment)['percentage']);
    }

    public function test_a_scale_option_records_its_configured_points(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $scale = GradingScale::factory()->create(['school_id' => $courseOffering->school_id]);
        $excellent = GradingScaleOption::factory()->create(['grading_scale_id' => $scale->id, 'label' => 'Excellent', 'points' => 5]);
        $secure = GradingScaleOption::factory()->create(['grading_scale_id' => $scale->id, 'label' => 'Secure', 'points' => 3]);
        $item = $this->item([
            'type' => GradeItemType::Scale->value,
            'grading_scale_id' => $scale->id,
            'max_points' => 5,
        ], $courseOffering);

        $entry = app(RecordGrade::class)->record($item, $this->enrollment(), gradingScaleOptionId: $secure->id);

        $this->assertSame($secure->id, $entry->grading_scale_option_id);
        $this->assertSame(3.0, $entry->points);
        $this->assertSame(60.0, app(GradebookCalculator::class)->calculate($courseOffering, $entry->studentRecord)['percentage']);
        $this->assertNotSame($excellent->id, $entry->grading_scale_option_id);
    }

    public function test_a_scale_item_refuses_an_option_from_another_scale(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $scale = GradingScale::factory()->create(['school_id' => $courseOffering->school_id]);
        $item = $this->item([
            'type' => GradeItemType::Scale->value,
            'grading_scale_id' => $scale->id,
            'max_points' => 5,
        ], $courseOffering);
        $outsideOption = GradingScaleOption::factory()->create();

        $this->expectException(InvalidValueException::class);

        app(RecordGrade::class)->record($item, $this->enrollment(), gradingScaleOptionId: $outsideOption->id);
    }

    public function test_categories_carry_their_own_weight(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();

        $classwork = $this->category($courseOffering, ['name' => 'Classwork', 'weight' => 1]);
        $exam = $this->category($courseOffering, ['name' => 'Final exam', 'weight' => 3]);

        $classworkItem = $this->item(['max_points' => 10, 'grade_category_id' => $classwork->id], $courseOffering);
        $examItem = $this->item(['max_points' => 100, 'grade_category_id' => $exam->id], $courseOffering);

        $action = app(RecordGrade::class);
        $action->record($classworkItem, $enrollment, points: 10);
        $action->record($examItem, $enrollment, points: 50);

        // (1.0 * 1 + 0.5 * 3) / 4 = 0.625
        $this->assertSame(62.5, app(GradebookCalculator::class)->calculate($courseOffering, $enrollment)['percentage']);
    }

    public function test_a_category_can_count_only_the_best_item(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        $category = $this->category($courseOffering, ['aggregation' => GradeAggregation::Highest->value]);

        $action = app(RecordGrade::class);
        $action->record($this->item(['max_points' => 10, 'grade_category_id' => $category->id], $courseOffering), $enrollment, points: 4);
        $action->record($this->item(['max_points' => 10, 'grade_category_id' => $category->id], $courseOffering), $enrollment, points: 9);

        $this->assertSame(90.0, app(GradebookCalculator::class)->calculate($courseOffering, $enrollment)['percentage']);
    }

    public function test_an_offering_without_marks_has_no_result(): void
    {
        $this->authorized_user([]);

        $this->assertNull(app(GradebookCalculator::class)->calculate($this->courseOffering(), $this->enrollment())['percentage']);
    }

    public function test_publishing_takes_a_copy_of_the_gradebook(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        app(RecordGrade::class)->record($this->item(['max_points' => 10], $courseOffering), $enrollment, points: 8);

        $snapshot = app(PublishResult::class)->publish($courseOffering, $enrollment);

        $this->assertSame(1, $snapshot->revision);
        $this->assertSame(80.0, $snapshot->percentage);
        $this->assertNotEmpty($snapshot->payload['items']);
    }

    public function test_a_student_outside_the_offering_cannot_receive_or_publish_a_result(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $item = $this->item(['max_points' => 10], $courseOffering);
        $outsideCycleSection = AcademicCycleSection::query()->findOrFail(AcademicCycleSection::factory()->create([
            'school_id' => $courseOffering->school_id,
            'academic_year_id' => $courseOffering->academic_year_id,
        ])->getKey());
        $outsideEnrollment = StudentRecord::query()->findOrFail(StudentRecord::factory()->create([
            'school_id' => $courseOffering->school_id,
            'academic_cycle_section_id' => $outsideCycleSection->id,
        ])->getKey());

        try {
            app(RecordGrade::class)->record($item, $outsideEnrollment, points: 8);
            $this->fail('Expected a student outside the course offering to be refused.');
        } catch (InvalidValueException) {
            $this->assertSame(0, GradeEntry::count());
        }

        $this->expectException(InvalidValueException::class);

        app(PublishResult::class)->publish($courseOffering, $outsideEnrollment);
    }

    public function test_a_published_result_does_not_follow_later_marks(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        $item = $this->item(['max_points' => 10], $courseOffering);
        app(RecordGrade::class)->record($item, $enrollment, points: 8);
        $snapshot = app(PublishResult::class)->publish($courseOffering, $enrollment);

        app(RecordGrade::class)->record($item, $enrollment, points: 3);

        $this->assertSame(80.0, $snapshot->fresh()->percentage);
    }

    public function test_a_correction_is_the_next_revision(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        $item = $this->item(['max_points' => 10], $courseOffering);
        $publish = app(PublishResult::class);
        app(RecordGrade::class)->record($item, $enrollment, points: 8);
        $publish->publish($courseOffering, $enrollment);

        app(RecordGrade::class)->record($item, $enrollment, points: 9);
        $corrected = $publish->publish($courseOffering, $enrollment, reason: 'Marking mistake');

        $this->assertSame(2, $corrected->revision);
        $this->assertSame(90.0, $corrected->percentage);
        $this->assertSame(2, ResultSnapshot::where('student_record_id', $enrollment->id)->count());
        $this->assertSame($corrected->id, $publish->current($courseOffering, $enrollment)?->id);
    }

    public function test_a_published_result_cannot_be_changed_or_deleted(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        app(RecordGrade::class)->record($this->item(['max_points' => 10], $courseOffering), $enrollment, points: 8);
        $snapshot = app(PublishResult::class)->publish($courseOffering, $enrollment);

        $this->expectException(RuntimeException::class);

        $snapshot->update(['percentage' => 100]);
    }

    public function test_publication_is_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $enrollment = $this->enrollment();
        app(RecordGrade::class)->record($this->item(['max_points' => 10], $courseOffering), $enrollment, points: 8);

        $snapshot = app(PublishResult::class)->publish($courseOffering, $enrollment);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::ResultPublished)->forSubject($snapshot)->first());
    }

    /**
     * Create an offering whose home section contains the test enrollment.
     */
    private function courseOffering(): CourseOffering
    {
        if ($this->courseOffering !== null) {
            return $this->courseOffering;
        }

        $school = $this->workingSchool();
        $academicYear = current_academic_year() ?? AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = current_academic_period() ?? AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $this->cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ]);
        $subject = Subject::factory()->create(['school_id' => $school->id]);

        $this->courseOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => $subject->id,
        ]);
        $this->courseOffering->cycleSections()->attach($this->cycleSection);

        return $this->courseOffering;
    }

    /**
     * Create a grade item in the given offering.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function item(array $attributes = [], ?CourseOffering $courseOffering = null): GradeItem
    {
        $courseOffering ??= $this->courseOffering();

        return GradeItem::create($attributes + [
            'school_id' => $courseOffering->school_id,
            'course_offering_id' => $courseOffering->id,
            'name' => 'Assessment',
        ]);
    }

    /**
     * Create a grade category in the given offering.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function category(CourseOffering $courseOffering, array $attributes = []): GradeCategory
    {
        return GradeCategory::create($attributes + [
            'school_id' => $courseOffering->school_id,
            'course_offering_id' => $courseOffering->id,
            'name' => 'Group',
        ]);
    }

    /**
     * Create an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        $courseOffering = $this->courseOffering();

        return StudentRecord::factory()->create([
            'school_id' => $courseOffering->school_id,
            'academic_cycle_section_id' => $this->cycleSection?->id,
        ]);
    }
}
