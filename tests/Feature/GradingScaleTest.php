<?php

namespace Tests\Feature;

use App\Actions\Gradebook\RecordGrade;
use App\Enums\GradeItemType;
use App\Enums\GradingScaleType;
use App\Livewire\GradingScaleManager;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\GradeItem;
use App\Models\GradingScale;
use App\Models\GradingScaleOption;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GradingScaleTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_school_administrator_can_create_a_scored_grading_scale(): void
    {
        $actor = $this->authorized_user(['manage grading scale']);

        $actor->get(route('grading-scales.index'))->assertOk()->assertSeeLivewire(GradingScaleManager::class);

        Livewire::test(GradingScaleManager::class)
            ->call('startCreating')
            ->set('name', 'Primary grades')
            ->set('description', 'Used for continuous assessment.')
            ->set('scaleType', GradingScaleType::Points->value)
            ->set('options', [
                ['id' => null, 'label' => 'Excellent', 'points' => '5'],
                ['id' => null, 'label' => 'Secure', 'points' => '3'],
                ['id' => null, 'label' => 'Developing', 'points' => '1'],
                ['id' => null, 'label' => '', 'points' => ''],
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('isEditing', false)
            ->assertSee('Primary grades');

        $scale = GradingScale::query()->inSchool()->firstOrFail();
        $this->assertSame('Primary grades', $scale->name);
        $this->assertTrue($scale->is_active);
        $this->assertSame(['Excellent', 'Secure', 'Developing'], $scale->options()->pluck('label')->all());
        $this->assertSame([5.0, 3.0, 1.0], $scale->options()->pluck('points')->map(fn ($points): float => (float) $points)->all());
    }

    public function test_scale_options_must_make_a_scale_teachers_can_mark_with(): void
    {
        $this->authorized_user(['manage grading scale']);

        Livewire::test(GradingScaleManager::class)
            ->call('startCreating')
            ->set('name', 'Mixed scale')
            ->set('scaleType', GradingScaleType::Points->value)
            ->set('options', [
                ['id' => null, 'label' => 'Excellent', 'points' => '5'],
                ['id' => null, 'label' => 'Secure', 'points' => ''],
            ])
            ->call('save')
            ->assertHasErrors('options')
            ->assertSee('Give every grade option points, or leave points blank for all of them.')
            ->set('options', [
                ['id' => null, 'label' => 'Good', 'points' => ''],
                ['id' => null, 'label' => 'good ', 'points' => ''],
            ])
            ->call('save')
            ->assertSee('Each grade option needs a different label.')
            ->set('options', [['id' => null, 'label' => 'Only', 'points' => '']])
            ->call('save')
            ->assertSee('Give the scale at least two grade options.')
            ->set('scaleType', GradingScaleType::Percentage->value)
            ->set('options', [
                ['id' => null, 'label' => 'A', 'points' => '120'],
                ['id' => null, 'label' => 'B', 'points' => '60'],
            ])
            ->call('save')
            ->assertSee('A percentage is between 0 and 100.')
            ->set('scaleType', GradingScaleType::Gpa->value)
            ->set('maximumValue', '4')
            ->set('options', [
                ['id' => null, 'label' => 'A', 'points' => '5'],
                ['id' => null, 'label' => 'B', 'points' => '3'],
            ])
            ->call('save')
            ->assertSee('A GPA value cannot be higher than the maximum GPA.');

        $this->assertSame(0, GradingScale::query()->count());
    }

    public function test_a_scale_from_another_school_cannot_be_changed_or_deleted(): void
    {
        $this->authorized_user(['manage grading scale']);
        $otherSchool = School::query()->findOrFail(School::factory()->create()->getKey());
        $scale = GradingScale::factory()->create(['school_id' => $otherSchool->id, 'name' => 'Their scale']);

        Livewire::test(GradingScaleManager::class)->assertDontSee('Their scale');

        $this->assertThrows(fn () => Livewire::test(GradingScaleManager::class)->call('startChanging', $scale->id), ModelNotFoundException::class);
        $this->assertThrows(fn () => Livewire::test(GradingScaleManager::class)->call('delete', $scale->id), ModelNotFoundException::class);
        $this->assertThrows(fn () => Livewire::test(GradingScaleManager::class)->call('setOffered', $scale->id, false), ModelNotFoundException::class);

        $this->assertTrue($scale->fresh()->is_active);
    }

    public function test_a_scale_somebody_else_saved_meanwhile_is_not_overwritten(): void
    {
        $this->authorized_user(['manage grading scale']);
        $scale = $this->scale();

        $late = Livewire::test(GradingScaleManager::class)->call('startChanging', $scale->id);

        $this->travel(2)->seconds();

        Livewire::test(GradingScaleManager::class)
            ->call('startChanging', $scale->id)
            ->call('addOption')
            ->set('options.2.label', 'Emerging')
            ->set('options.2.points', '1')
            ->call('save')
            ->assertHasNoErrors();

        $late->set('name', 'Renamed')
            ->call('save')
            ->assertHasErrors('options')
            ->assertSee('Somebody else saved this scale while you worked on it.');

        $this->assertSame(['Excellent', 'Secure', 'Emerging'], $scale->options()->pluck('label')->all());
        $this->assertNotSame('Renamed', $scale->fresh()->name);
    }

    public function test_an_option_used_in_a_learner_record_is_locked(): void
    {
        $this->authorized_user(['manage grading scale']);
        $scale = $this->scale();
        $secure = $scale->options()->where('label', 'Secure')->sole();
        $this->recordGradeWith($scale, $secure);

        $component = Livewire::test(GradingScaleManager::class)
            ->assertDontSeeHtml('wire:click="delete('.$scale->id.')"')
            ->call('startChanging', $scale->id)
            ->assertSet('recordedOptionIds', [$secure->id])
            ->call('removeOption', 1)
            ->assertHasErrors('options')
            ->set('options.1.label', 'Sure')
            ->call('save')
            ->assertSee('A grade option already used in a learner record cannot be changed.');

        $component->set('options.1.label', 'Secure')
            ->set('scaleType', GradingScaleType::Descriptive->value)
            ->set('options.0.points', '')
            ->set('options.1.points', '')
            ->call('save')
            ->assertSee('cannot change its basis or maximum value');

        $this->assertSame('Secure', $secure->fresh()->label);

        Livewire::test(GradingScaleManager::class)
            ->call('delete', $scale->id)
            ->assertDispatched('status-message', type: 'danger', message: 'This scale is used by an assessment. Stop offering it instead of deleting it.')
            ->call('setOffered', $scale->id, false)
            ->assertSee('not offered for new assessments');

        $this->assertNotNull($scale->fresh());
        $this->assertFalse($scale->fresh()->is_active);
    }

    public function test_an_unused_scale_is_deleted_with_a_named_warning(): void
    {
        $actor = $this->authorized_user(['manage grading scale']);
        $scale = $this->scale();

        $actor->get(route('grading-scales.index'))->assertOk()
            ->assertSee('wire:confirm="Delete '.e($scale->name).'? It cannot be brought back."', false);

        Livewire::test(GradingScaleManager::class)
            ->call('delete', $scale->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertNull($scale->fresh());
    }

    public function test_a_user_without_the_permission_sees_nothing(): void
    {
        $this->authorized_user([]);

        Livewire::test(GradingScaleManager::class)->assertForbidden();
    }

    private function scale(): GradingScale
    {
        $scale = GradingScale::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Primary grades',
            'scale_type' => GradingScaleType::Points->value,
        ]);
        GradingScaleOption::factory()->create(['grading_scale_id' => $scale->id, 'label' => 'Excellent', 'points' => 5, 'position' => 1]);
        GradingScaleOption::factory()->create(['grading_scale_id' => $scale->id, 'label' => 'Secure', 'points' => 3, 'position' => 2]);

        return $scale->fresh();
    }

    /**
     * Give one learner a grade from the scale.
     */
    private function recordGradeWith(GradingScale $scale, GradingScaleOption $option): void
    {
        $school = $this->workingSchool();
        $academicYear = current_academic_year() ?? AcademicYear::query()->findOrFail(AcademicYear::factory()->create(['school_id' => $school->id])->getKey());
        $academicPeriod = current_academic_period() ?? AcademicPeriod::query()->findOrFail(AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ])->getKey());
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $section = AcademicCycleSection::factory()->create([
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
        $courseOffering->cycleSections()->attach($section);
        $item = GradeItem::create([
            'school_id' => $school->id,
            'course_offering_id' => $courseOffering->id,
            'name' => 'Reading check',
            'type' => GradeItemType::Scale->value,
            'grading_scale_id' => $scale->id,
            'max_points' => 5,
        ]);
        $learner = StudentRecord::factory()->create(['school_id' => $school->id, 'academic_cycle_section_id' => $section->id]);

        app(RecordGrade::class)->record($item, $learner, gradingScaleOptionId: $option->id);
    }
}
