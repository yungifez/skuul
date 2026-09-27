<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Enums\GradeAggregation;
use App\Enums\GradeItemType;
use App\Enums\ResultApprovalStatus;
use App\Livewire\GradebookMarkSheet;
use App\Livewire\GradebookSetup;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AssessmentTemplate;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GradebookScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_staff_can_configure_freehand_assessments(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'update subject']);
        [$courseOffering] = $this->offeringAndEnrollment();

        $setup = Livewire::test(GradebookSetup::class, ['courseOffering' => $courseOffering])
            ->set('isAddingCategory', true)
            ->set('categoryName', 'Exams')
            ->set('categoryAggregation', GradeAggregation::WeightedMean->value)
            ->set('categoryWeight', '2')
            ->call('addCategory')
            ->assertHasNoErrors();

        $category = GradeCategory::query()->whereBelongsTo($courseOffering)->sole();

        $setup->set('itemName', 'Mathematics paper')
            ->set('itemType', GradeItemType::Numeric->value)
            ->set('itemMaxPoints', '60')
            ->set('itemCategoryId', (string) $category->id)
            ->call('saveItem')
            ->assertHasNoErrors();

        $item = GradeItem::query()->whereBelongsTo($courseOffering)->sole();
        $this->assertNull($item->exam_slot_id);
        $this->assertSame(60.0, $item->max_points);
        $this->assertSame($category->id, $item->grade_category_id);

        $setup->call('editItem', $item->id)
            ->set('itemName', 'Mathematics paper revised')
            ->set('itemCategoryId', '')
            ->set('itemWeight', '3')
            ->set('itemDueOn', '2026-09-02')
            ->call('saveItem')
            ->assertHasNoErrors();

        $this->assertSame('Mathematics paper revised', $item->fresh()->name);
        $this->assertSame(3.0, $item->fresh()->weight);
        $this->assertSame('2026-09-02', $item->fresh()->due_on->format('Y-m-d'));
    }

    public function test_staff_can_manage_and_publish_from_the_offering_gradebook_screen(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'publish result', 'approve result', 'update subject']);
        [$courseOffering, $enrollment] = $this->offeringAndEnrollment();

        $this->get(route('course-offerings.gradebook.show', $courseOffering))
            ->assertOk()
            ->assertSeeLivewire(GradebookSetup::class)
            ->assertSee('Add assessment')
            ->assertSee('Editing open')
            ->assertSeeLivewire(GradebookMarkSheet::class);

        $setup = Livewire::test(GradebookSetup::class, ['courseOffering' => $courseOffering])
            ->set('itemName', 'Term project')
            ->set('itemMaxPoints', '20')
            ->call('saveItem')
            ->assertHasNoErrors();

        $item = GradeItem::query()->whereBelongsTo($courseOffering)->firstOrFail();

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $courseOffering])
            ->set("marks.$enrollment->id.points", '16')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(16.0, GradeEntry::query()->firstOrFail()->points);

        $setup->call('startAddingItem')
            ->set('itemName', 'Learning reflection')
            ->set('itemType', GradeItemType::Text->value)
            ->call('saveItem')
            ->assertHasNoErrors();

        $commentItem = GradeItem::query()->whereBelongsTo($courseOffering)->latest('id')->firstOrFail();

        Livewire::withQueryParams(['assessment' => $commentItem->id])
            ->test(GradebookMarkSheet::class, ['courseOffering' => $courseOffering])
            ->set("marks.$enrollment->id.comment", 'Reads with confidence.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Reads with confidence.', GradeEntry::query()->latest('id')->firstOrFail()->comment);

        $sheet = Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $courseOffering])
            ->call('submitResult', $enrollment->id);

        $snapshot = ResultSnapshot::query()->firstOrFail();
        $this->assertSame(80.0, $snapshot->percentage);
        $this->assertSame(ResultApprovalStatus::Pending, $snapshot->approval_status);

        $sheet->call('approveResult', $snapshot->id);

        $this->assertSame(ResultApprovalStatus::Approved, $snapshot->fresh()->approval_status);
    }

    public function test_a_closed_gradebook_explains_that_editing_is_locked(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'update subject']);
        [$courseOffering] = $this->offeringAndEnrollment();
        $courseOffering->academicPeriod()->update(['status' => AcademicPeriodStatus::Closed->value]);

        $this->get(route('course-offerings.gradebook.show', $courseOffering))
            ->assertOk()
            ->assertSee('This gradebook is read-only.')
            ->assertSee('Read-only')
            ->assertSee('No assessments yet')
            ->assertSee('assessment setup and mark entry are locked.')
            ->assertDontSeeLivewire(GradebookSetup::class)
            ->assertDontSee('Add an assessment');
    }

    public function test_a_closing_gradebook_allows_corrections_but_not_new_assessments(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'update subject']);
        [$courseOffering] = $this->offeringAndEnrollment();
        $courseOffering->academicPeriod()->update(['status' => AcademicPeriodStatus::Closing->value]);
        GradeItem::create([
            'school_id' => $courseOffering->school_id,
            'course_offering_id' => $courseOffering->id,
            'name' => 'Classwork',
            'type' => GradeItemType::Numeric,
            'max_points' => 20,
        ]);

        $this->get(route('course-offerings.gradebook.show', $courseOffering))
            ->assertOk()
            ->assertSee('Corrections open')
            ->assertSee('Save marks')
            ->assertDontSeeLivewire(GradebookSetup::class)
            ->assertDontSee('Add an assessment')
            ->assertDontSee('This gradebook is read-only.');
    }

    public function test_staff_can_save_and_apply_a_school_assessment_template_from_the_gradebook_screen(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'update subject']);
        [$source] = $this->offeringAndEnrollment();
        GradeItem::create([
            'school_id' => $source->school_id,
            'course_offering_id' => $source->id,
            'name' => 'Classwork',
            'type' => GradeItemType::Numeric,
            'max_points' => 20,
        ]);

        Livewire::test(GradebookSetup::class, ['courseOffering' => $source])
            ->set('isSavingTemplate', true)
            ->set('templateName', 'Common term assessment')
            ->set('templateDescription', 'Use for all term-based courses.')
            ->call('saveTemplate')
            ->assertHasNoErrors();

        $template = AssessmentTemplate::query()->sole();
        [$target] = $this->offeringAndEnrollment();

        Livewire::test(GradebookSetup::class, ['courseOffering' => $target])
            ->assertSee('Start from a school template')
            ->assertSee('Common term assessment')
            ->set('templateId', (string) $template->id)
            ->call('applyTemplate')
            ->assertHasNoErrors();

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
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ])->getKey());
        $academicLevel = AcademicLevel::query()->findOrFail(AcademicLevel::factory()->create([
            'school_id' => $school->id,
        ])->getKey());
        $cycleSection = AcademicCycleSection::query()->findOrFail(AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ])->getKey());
        $subject = Subject::query()->findOrFail(Subject::factory()->create(['school_id' => $school->id])->getKey());
        $courseOffering = CourseOffering::query()->findOrFail(CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => $subject->id,
        ])->getKey());
        $courseOffering->cycleSections()->attach($cycleSection);
        $student = User::query()->create(User::factory()->raw());
        $enrollment = StudentRecord::query()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => $cycleSection->id,
            'user_id' => $student->id,
            'admission_date' => now(),
        ]);

        return [$courseOffering, $enrollment];
    }
}
