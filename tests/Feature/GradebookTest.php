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
use App\Models\AuditEvent;
use App\Models\ClassGroup;
use App\Models\GradeCategory;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\MyClass;
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
        app(ChangeAcademicPeriodStatus::class)->close($item->academicYear);

        $this->expectException(ClosedPeriodException::class);

        app(RecordGrade::class)->record($item->fresh(), $this->enrollment(), points: 10);
    }

    public function test_the_result_is_a_share_of_the_maximum(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        $first = $this->item(['max_points' => 20], $subject);
        $second = $this->item(['max_points' => 50], $subject);

        $action = app(RecordGrade::class);
        $action->record($first, $enrollment, points: 10);
        $action->record($second, $enrollment, points: 25);

        $result = app(GradebookCalculator::class)->calculate($subject, $enrollment);

        $this->assertSame(50.0, $result['percentage']);
    }

    public function test_excused_work_leaves_the_total_alone(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        $marked = $this->item(['max_points' => 20], $subject);
        $excused = $this->item(['max_points' => 100], $subject);

        $action = app(RecordGrade::class);
        $action->record($marked, $enrollment, points: 20);
        $action->record($excused, $enrollment, GradeEntryState::Exempt);

        $result = app(GradebookCalculator::class)->calculate($subject, $enrollment);

        $this->assertSame(100.0, $result['percentage']);
    }

    public function test_missing_work_counts_as_nothing(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        $marked = $this->item(['max_points' => 10], $subject);
        $missed = $this->item(['max_points' => 10], $subject);

        $action = app(RecordGrade::class);
        $action->record($marked, $enrollment, points: 10);
        $action->record($missed, $enrollment, GradeEntryState::Missing);

        $result = app(GradebookCalculator::class)->calculate($subject, $enrollment);

        $this->assertSame(50.0, $result['percentage']);
    }

    public function test_a_comment_item_does_not_change_the_total(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        $marked = $this->item(['max_points' => 10], $subject);
        $note = $this->item(['type' => GradeItemType::Text->value, 'max_points' => null], $subject);

        $action = app(RecordGrade::class);
        $action->record($marked, $enrollment, points: 7);
        $action->record($note, $enrollment, comment: 'Reads with confidence.');

        $this->assertSame(70.0, app(GradebookCalculator::class)->calculate($subject, $enrollment)['percentage']);
    }

    public function test_categories_carry_their_own_weight(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();

        $classwork = $this->category($subject, ['name' => 'Classwork', 'weight' => 1]);
        $exam = $this->category($subject, ['name' => 'Final exam', 'weight' => 3]);

        $classworkItem = $this->item(['max_points' => 10, 'grade_category_id' => $classwork->id], $subject);
        $examItem = $this->item(['max_points' => 100, 'grade_category_id' => $exam->id], $subject);

        $action = app(RecordGrade::class);
        $action->record($classworkItem, $enrollment, points: 10);
        $action->record($examItem, $enrollment, points: 50);

        // (1.0 * 1 + 0.5 * 3) / 4 = 0.625
        $this->assertSame(62.5, app(GradebookCalculator::class)->calculate($subject, $enrollment)['percentage']);
    }

    public function test_a_category_can_count_only_the_best_item(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        $category = $this->category($subject, ['aggregation' => GradeAggregation::Highest->value]);

        $action = app(RecordGrade::class);
        $action->record($this->item(['max_points' => 10, 'grade_category_id' => $category->id], $subject), $enrollment, points: 4);
        $action->record($this->item(['max_points' => 10, 'grade_category_id' => $category->id], $subject), $enrollment, points: 9);

        $this->assertSame(90.0, app(GradebookCalculator::class)->calculate($subject, $enrollment)['percentage']);
    }

    public function test_a_subject_without_marks_has_no_result(): void
    {
        $this->authorized_user([]);

        $this->assertNull(app(GradebookCalculator::class)->calculate($this->subject(), $this->enrollment())['percentage']);
    }

    public function test_publishing_takes_a_copy_of_the_gradebook(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        app(RecordGrade::class)->record($this->item(['max_points' => 10], $subject), $enrollment, points: 8);

        $snapshot = app(PublishResult::class)->publish($subject, $enrollment);

        $this->assertSame(1, $snapshot->revision);
        $this->assertSame(80.0, $snapshot->percentage);
        $this->assertNotEmpty($snapshot->payload['items']);
    }

    public function test_a_published_result_does_not_follow_later_marks(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        $item = $this->item(['max_points' => 10], $subject);
        app(RecordGrade::class)->record($item, $enrollment, points: 8);
        $snapshot = app(PublishResult::class)->publish($subject, $enrollment);

        app(RecordGrade::class)->record($item, $enrollment, points: 3);

        $this->assertSame(80.0, $snapshot->fresh()->percentage);
    }

    public function test_a_correction_is_the_next_revision(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        $item = $this->item(['max_points' => 10], $subject);
        $publish = app(PublishResult::class);
        app(RecordGrade::class)->record($item, $enrollment, points: 8);
        $publish->publish($subject, $enrollment);

        app(RecordGrade::class)->record($item, $enrollment, points: 9);
        $corrected = $publish->publish($subject, $enrollment, reason: 'Marking mistake');

        $this->assertSame(2, $corrected->revision);
        $this->assertSame(90.0, $corrected->percentage);
        $this->assertSame(2, ResultSnapshot::where('student_record_id', $enrollment->id)->count());
        $this->assertSame($corrected->id, $publish->current($subject, $enrollment)?->id);
    }

    public function test_a_published_result_cannot_be_changed_or_deleted(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        app(RecordGrade::class)->record($this->item(['max_points' => 10], $subject), $enrollment, points: 8);
        $snapshot = app(PublishResult::class)->publish($subject, $enrollment);

        $this->expectException(RuntimeException::class);

        $snapshot->update(['percentage' => 100]);
    }

    public function test_publication_is_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        app(RecordGrade::class)->record($this->item(['max_points' => 10], $subject), $enrollment, points: 8);

        $snapshot = app(PublishResult::class)->publish($subject, $enrollment);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::ResultPublished)->forSubject($snapshot)->first());
    }

    /**
     * Create a subject in the working school.
     */
    private function subject(): Subject
    {
        $classGroup = ClassGroup::factory()->create(['school_id' => $this->workingSchool()->id]);
        $class = MyClass::factory()->create(['class_group_id' => $classGroup->id]);

        return Subject::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'my_class_id' => $class->id,
        ]);
    }

    /**
     * Create a grade item in the given subject.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function item(array $attributes = [], ?Subject $subject = null): GradeItem
    {
        $subject ??= $this->subject();

        return GradeItem::create($attributes + [
            'school_id' => $subject->school_id,
            'subject_id' => $subject->id,
            'academic_year_id' => current_academic_year_id(),
            'semester_id' => current_semester_id(),
            'name' => 'Assessment',
        ]);
    }

    /**
     * Create a grade category in the given subject.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function category(Subject $subject, array $attributes = []): GradeCategory
    {
        return GradeCategory::create($attributes + [
            'school_id' => $subject->school_id,
            'subject_id' => $subject->id,
            'academic_year_id' => current_academic_year_id(),
            'name' => 'Group',
        ]);
    }

    /**
     * Create an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }
}
