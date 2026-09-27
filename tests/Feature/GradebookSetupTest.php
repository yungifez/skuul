<?php

namespace Tests\Feature;

use App\Actions\Gradebook\RecordGrade;
use App\Enums\GradeItemType;
use App\Livewire\GradebookMarkSheet;
use App\Livewire\GradebookSetup;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AssessmentTemplate;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\GradeItem;
use App\Models\GradingScale;
use App\Models\GradingScaleOption;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Setting up the assessments must never break a mark a learner already has.
 */
class GradebookSetupTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private const array TEACHER = ['read gradebook', 'manage gradebook', 'update subject'];

    private ?AcademicCycleSection $cycleSection = null;

    public function test_a_reader_cannot_change_the_setup(): void
    {
        $courseOffering = $this->courseOffering();
        $this->authorized_user(['read gradebook', 'update subject']);

        Livewire::test(GradebookSetup::class, ['courseOffering' => $courseOffering])->assertForbidden();
    }

    public function test_the_maximum_cannot_drop_below_a_mark_already_given(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        app(RecordGrade::class)->record($item, $this->enrollment(), points: 18);

        $setup = Livewire::test(GradebookSetup::class, ['courseOffering' => $item->courseOffering])
            ->call('editItem', $item->id)
            ->set('itemMaxPoints', '10')
            ->call('saveItem')
            ->assertHasErrors('itemMaxPoints')
            ->assertSee('A learner already has 18. The maximum cannot go below that.');

        $this->assertSame(20.0, $item->fresh()->max_points);

        $setup->set('itemMaxPoints', '18')->call('saveItem')->assertHasNoErrors();

        $this->assertSame(18.0, $item->fresh()->max_points);
    }

    public function test_the_kind_of_mark_does_not_change_once_the_assessment_exists(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);

        Livewire::test(GradebookSetup::class, ['courseOffering' => $item->courseOffering])
            ->call('editItem', $item->id)
            ->set('itemType', GradeItemType::Text->value)
            ->call('saveItem')
            ->assertHasNoErrors();

        $this->assertSame(GradeItemType::Numeric, $item->fresh()->type);
        $this->assertSame(20.0, $item->fresh()->max_points);
    }

    public function test_an_assessment_with_marks_stays_and_one_without_goes(): void
    {
        $this->authorized_user(self::TEACHER);
        $marked = $this->item(['name' => 'Marked', 'max_points' => 20]);
        $empty = $this->item(['name' => 'Empty', 'max_points' => 20]);
        app(RecordGrade::class)->record($marked, $this->enrollment(), points: 5);

        Livewire::test(GradebookSetup::class, ['courseOffering' => $marked->courseOffering])
            ->call('removeItem', $marked->id)
            ->assertDispatched('status-message', type: 'danger', message: 'Marked has marks. Remove them first.')
            ->call('removeItem', $empty->id)
            ->assertDispatched('gradebook-changed');

        $this->assertNotNull($marked->fresh());
        $this->assertNull($empty->fresh());
    }

    public function test_the_mark_sheet_follows_a_new_assessment(): void
    {
        $this->authorized_user(self::TEACHER);
        $courseOffering = $this->courseOffering();
        $this->enrollment();

        $sheet = Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $courseOffering])
            ->assertSee('No assessments yet');

        Livewire::test(GradebookSetup::class, ['courseOffering' => $courseOffering])
            ->assertSet('isAddingItem', true)
            ->set('itemName', 'Spelling')
            ->set('itemMaxPoints', '10')
            ->call('saveItem')
            ->assertHasNoErrors();

        $sheet->dispatch('gradebook-changed')
            ->assertSet('gradeItemId', (string) GradeItem::sole()->id)
            ->assertSee('Save marks');
    }

    public function test_another_courses_category_and_another_schools_scale_are_refused(): void
    {
        $this->authorized_user(self::TEACHER);
        $courseOffering = $this->courseOffering();
        $otherCourse = $this->courseOffering(fresh: true);
        $foreignCategory = GradeCategory::create(['school_id' => $otherCourse->school_id, 'course_offering_id' => $otherCourse->id, 'name' => 'Theirs', 'aggregation' => 'weighted_mean', 'weight' => 1, 'position' => 1]);
        $foreignScale = GradingScale::factory()->create(['school_id' => School::factory()->create()->id]);

        Livewire::test(GradebookSetup::class, ['courseOffering' => $courseOffering])
            ->set('itemName', 'Spelling')
            ->set('itemType', GradeItemType::Scale->value)
            ->set('itemScaleId', (string) $foreignScale->id)
            ->set('itemCategoryId', (string) $foreignCategory->id)
            ->call('saveItem')
            ->assertHasErrors(['itemScaleId', 'itemCategoryId']);

        $this->assertSame(0, GradeItem::query()->count());
    }

    public function test_a_scale_assessment_takes_its_maximum_from_the_scale(): void
    {
        $this->authorized_user(self::TEACHER);
        $courseOffering = $this->courseOffering();
        $scale = GradingScale::factory()->create(['school_id' => $courseOffering->school_id]);
        GradingScaleOption::factory()->create(['grading_scale_id' => $scale->id, 'label' => 'Excellent', 'points' => 5]);
        GradingScaleOption::factory()->create(['grading_scale_id' => $scale->id, 'label' => 'Secure', 'points' => 3]);

        Livewire::test(GradebookSetup::class, ['courseOffering' => $courseOffering])
            ->set('itemName', 'Reading')
            ->set('itemType', GradeItemType::Scale->value)
            ->call('saveItem')
            ->assertHasErrors(['itemScaleId' => 'required'])
            ->set('itemScaleId', (string) $scale->id)
            ->set('itemMaxPoints', '99')
            ->call('saveItem')
            ->assertHasNoErrors();

        $item = GradeItem::sole();
        $this->assertSame($scale->id, $item->grading_scale_id);
        $this->assertSame(5.0, $item->max_points);
    }

    public function test_a_category_name_is_used_once_per_course_and_a_typo_year_is_refused(): void
    {
        $this->authorized_user(self::TEACHER);
        $courseOffering = $this->courseOffering();

        Livewire::test(GradebookSetup::class, ['courseOffering' => $courseOffering])
            ->set('categoryName', 'Exams')
            ->call('addCategory')
            ->assertHasNoErrors()
            ->set('categoryName', 'Exams')
            ->call('addCategory')
            ->assertHasErrors('categoryName')
            ->set('itemName', 'Mock')
            ->set('itemMaxPoints', '50')
            ->set('itemDueOn', '20266-09-02')
            ->call('saveItem')
            ->assertHasErrors('itemDueOn');

        $this->assertSame(1, GradeCategory::query()->count());
        $this->assertSame(0, GradeItem::query()->count());
    }

    public function test_a_template_name_is_used_once_per_school(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        AssessmentTemplate::create(['school_id' => $item->school_id, 'name' => 'Term plan', 'is_active' => true]);

        Livewire::test(GradebookSetup::class, ['courseOffering' => $item->courseOffering])
            ->set('isSavingTemplate', true)
            ->set('templateName', 'Term plan')
            ->call('saveTemplate')
            ->assertHasErrors('templateName')
            ->assertSee('Your school already has a template with this name.');

        $this->assertSame(1, AssessmentTemplate::query()->count());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function item(array $attributes = []): GradeItem
    {
        $courseOffering = $this->courseOffering();

        return GradeItem::create($attributes + [
            'school_id' => $courseOffering->school_id,
            'course_offering_id' => $courseOffering->id,
            'name' => 'Assessment',
            'type' => GradeItemType::Numeric->value,
        ]);
    }

    private function enrollment(): StudentRecord
    {
        $courseOffering = $this->courseOffering();

        return StudentRecord::factory()->create([
            'school_id' => $courseOffering->school_id,
            'academic_cycle_section_id' => $this->cycleSection?->id,
        ]);
    }

    private ?CourseOffering $courseOffering = null;

    private function courseOffering(bool $fresh = false): CourseOffering
    {
        if (!$fresh && $this->courseOffering !== null) {
            return $this->courseOffering;
        }

        $school = $this->workingSchool();
        $academicYear = current_academic_year() ?? AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = current_academic_period() ?? AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ]);
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => Subject::factory()->create(['school_id' => $school->id])->id,
        ]);
        $courseOffering->cycleSections()->attach($cycleSection);

        if (!$fresh) {
            $this->cycleSection = $cycleSection;
            $this->courseOffering = $courseOffering;
        }

        return $courseOffering;
    }
}
