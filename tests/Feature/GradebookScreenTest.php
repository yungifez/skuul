<?php

namespace Tests\Feature;

use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AssessmentTemplate;
use App\Models\CourseOffering;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradebookScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_staff_can_manage_and_publish_from_the_offering_gradebook_screen(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'publish result', 'update subject']);
        [$courseOffering, $enrollment] = $this->offeringAndEnrollment();

        $this->get(route('course-offerings.gradebook.show', $courseOffering))
            ->assertOk()
            ->assertSee('Record grades and publish results');

        $this->post(route('course-offerings.gradebook.items.store', $courseOffering), [
            'name'       => 'Term project',
            'type'       => GradeItemType::Numeric->value,
            'max_points' => 20,
            'weight'     => 1,
        ])->assertSessionHas('success');

        $item = GradeItem::query()->whereBelongsTo($courseOffering)->firstOrFail();

        $this->post(route('course-offerings.gradebook.entries.store', $courseOffering), [
            'grade_item_id'     => $item->id,
            'student_record_id' => $enrollment->id,
            'state'             => GradeEntryState::Graded->value,
            'points'            => 16,
        ])->assertSessionHas('success');

        $this->assertSame(16.0, GradeEntry::query()->firstOrFail()->points);

        $this->post(route('course-offerings.gradebook.results.publish', $courseOffering), [
            'student_record_id' => $enrollment->id,
        ])->assertSessionHas('success');

        $this->assertSame(80.0, ResultSnapshot::query()->firstOrFail()->percentage);
    }

    public function test_staff_can_save_and_apply_a_school_assessment_template_from_the_gradebook_screen(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'update subject']);
        [$source] = $this->offeringAndEnrollment();
        GradeItem::create([
            'school_id'          => $source->school_id,
            'course_offering_id' => $source->id,
            'name'               => 'Classwork',
            'type'               => GradeItemType::Numeric,
            'max_points'         => 20,
        ]);

        $this->post(route('course-offerings.gradebook.templates.store', $source), [
            'template_name' => 'Common term assessment',
            'description'   => 'Use for all term-based courses.',
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $template = AssessmentTemplate::query()->sole();
        [$target] = $this->offeringAndEnrollment();

        $this->get(route('course-offerings.gradebook.show', $target))
            ->assertOk()
            ->assertSee('Start from a school template')
            ->assertSee('Common term assessment');

        $this->post(route('course-offerings.gradebook.templates.apply', $target), [
            'assessment_template_id' => $template->id,
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertSame('Classwork', $target->gradeItems()->sole()->name);
    }

    /**
     * @return array{CourseOffering, StudentRecord}
     */
    private function offeringAndEnrollment(): array
    {
        $school = $this->workingSchool();
        $academicYear = current_academic_year() ?? AcademicYear::query()->findOrFail(AcademicYear::factory()->create([
            'school_id' => $school->id,
        ])->getKey());
        $academicPeriod = current_academic_period() ?? AcademicPeriod::query()->findOrFail(AcademicPeriod::factory()->create([
            'school_id'        => $school->id,
            'academic_year_id' => $academicYear->id,
        ])->getKey());
        $academicLevel = AcademicLevel::query()->findOrFail(AcademicLevel::factory()->create([
            'school_id' => $school->id,
        ])->getKey());
        $cycleSection = AcademicCycleSection::query()->findOrFail(AcademicCycleSection::factory()->create([
            'school_id'         => $school->id,
            'academic_year_id'  => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ])->getKey());
        $subject = Subject::query()->findOrFail(Subject::factory()->create(['school_id' => $school->id])->getKey());
        $courseOffering = CourseOffering::query()->findOrFail(CourseOffering::factory()->create([
            'school_id'          => $school->id,
            'academic_year_id'   => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id'  => $academicLevel->id,
            'subject_id'         => $subject->id,
        ])->getKey());
        $courseOffering->cycleSections()->attach($cycleSection);
        $student = User::query()->create(User::factory()->raw());
        $enrollment = StudentRecord::query()->create([
            'school_id'                 => $school->id,
            'academic_cycle_section_id' => $cycleSection->id,
            'user_id'                   => $student->id,
            'admission_date'            => now(),
        ]);

        return [$courseOffering, $enrollment];
    }
}
