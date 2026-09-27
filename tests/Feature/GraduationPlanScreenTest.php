<?php

namespace Tests\Feature;

use App\Enums\AuditAction;
use App\Livewire\CreateGraduationPlanForm;
use App\Livewire\GraduationPlanRecord;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\GraduationExemption;
use App\Models\GraduationPlan;
use App\Models\GraduationRequirement;
use App\Models\ResultSnapshot;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Services\Graduation\GraduationProgress;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A graduation plan says what a learner must finish, and only a published
 * result moves them along it.
 */
class GraduationPlanScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_list_starts_empty(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);

        $this->get(route('graduation-plans.index'))
            ->assertOk()
            ->assertSee('No graduation plans yet')
            ->assertSee(route('graduation-plans.create'));
    }

    public function test_writing_a_plan_starts_with_simple_school_rules(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);

        $this->get(route('graduation-plans.create'))
            ->assertOk()
            ->assertSeeLivewire(CreateGraduationPlanForm::class)
            ->assertSee('Start with the basics')
            ->assertSee('Every item is required by default')
            ->assertSee('Advanced rules (optional)')
            ->assertDontSeeHtml('py-1" open');
    }

    public function test_a_plan_is_written_from_the_screen(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);

        Livewire::test(CreateGraduationPlanForm::class)
            ->set('name', ' Senior school diploma ')
            ->set('usesCredits', true)
            ->set('requiredCredits', '24')
            ->set('requiredCount', '9')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('graduation-plans.show', GraduationPlan::inSchool()->sole()));

        $plan = GraduationPlan::inSchool()->sole();
        $this->assertSame('Senior school diploma', $plan->name);
        $this->assertTrue($plan->uses_credits);
        $this->assertSame(24, $plan->required_credits);
        $this->assertNull($plan->required_count);
        $this->assertTrue(AuditEvent::query()->where('action', AuditAction::GraduationPlanChanged->value)->exists());
    }

    public function test_a_plan_that_counts_credits_must_say_how_many(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);

        Livewire::test(CreateGraduationPlanForm::class)
            ->set('name', 'Senior school diploma')
            ->set('usesCredits', true)
            ->call('save')
            ->assertHasErrors('requiredCredits')
            ->set('usesCredits', false)
            ->set('completionOperator', 'at_least')
            ->call('save')
            ->assertHasErrors('requiredCount');

        $this->assertSame(0, GraduationPlan::inSchool()->count());
    }

    public function test_a_plan_and_a_stage_never_share_a_name_whatever_the_capitals(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();

        Livewire::test(CreateGraduationPlanForm::class)
            ->set('name', 'SENIOR school diploma')
            ->call('save')
            ->assertHasErrors('name');

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->set('stageName', 'senior School Diploma')
            ->call('addStage')
            ->assertHasErrors('stageName');

        $this->assertSame(1, GraduationPlan::inSchool()->count());
    }

    public function test_a_requirement_is_added_and_removed(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);

        $this->get(route('graduation-plans.show', $plan))->assertOk()->assertSeeLivewire(GraduationPlanRecord::class);

        $component = Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->assertSee('Asks for nothing yet')
            ->set('requirementDescription', 'Pass mathematics')
            ->set('requirementSubjectId', (string) $subject->id)
            ->set('requirementCredits', '3')
            ->call('addRequirement')
            ->assertHasNoErrors()
            ->assertSee('Pass mathematics')
            ->assertSee('Must be met')
            ->assertDontSee('Asks for nothing yet');

        $requirement = $plan->requirements()->sole();
        $this->assertSame(3, $requirement->credits);

        $component->call('removeRequirement', $requirement->id);

        $this->assertSame(0, $plan->requirements()->count());
    }

    public function test_a_plan_can_have_ordered_nested_stages_and_logic(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->set('stageName', 'KG 1')
            ->call('addStage')
            ->assertHasNoErrors()
            ->set('stageName', 'Later kindergarten')
            ->set('stageCompletionOperator', 'any')
            ->call('addStage')
            ->set('stageName', 'Electives')
            ->set('stageCompletionOperator', 'at_least')
            ->call('addStage')
            ->assertHasErrors('stageRequiredCount')
            ->set('stageRequiredCount', '4')
            ->call('addStage')
            ->assertHasNoErrors();

        $choice = $plan->children()->where('name', 'Later kindergarten')->sole();

        Livewire::test(GraduationPlanRecord::class, ['plan' => $choice])
            ->assertSee('A stage inside')
            ->set('stageName', 'KG 2')
            ->call('addStage')
            ->assertHasNoErrors();

        $electives = $plan->children()->where('name', 'Electives')->sole();

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->assertSee('KG 1')
            ->assertSee('Later kindergarten')
            ->assertSee('KG 2')
            ->assertSee('Any item (OR)')
            ->assertSee('At least 4 items');

        Livewire::test(GraduationPlanRecord::class, ['plan' => $electives])
            ->assertSee('Asks for nothing yet');

        $this->assertSame([0, 1, 2], $plan->children()->orderBy('position')->pluck('position')->all());
        $this->assertSame($choice->id, $choice->children()->sole()->parent_id);

        $this->get(route('graduation-plans.index'))
            ->assertOk()
            ->assertSee(route('graduation-plans.show', $plan))
            ->assertDontSee(route('graduation-plans.show', $choice));
    }

    public function test_a_stage_that_asks_for_more_items_than_it_holds_says_so(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $plan->update(['completion_operator' => 'at_least', 'required_count' => 3]);
        $this->requirement($plan, null);

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->assertSee('Asks for 3 items but holds 1, so no learner can finish it.');
    }

    public function test_a_stage_can_require_credits_from_its_subjects(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->set('stageName', 'Science credits')
            ->set('stageCompletionOperator', 'at_least_credits')
            ->set('stageRequiredCredits', '3')
            ->call('addStage')
            ->assertHasNoErrors()
            ->assertSee('At least 3 credits');

        $stage = $plan->children()->sole();

        $this->assertTrue($stage->uses_credits);
        $this->assertSame(3, $stage->required_credits);
    }

    public function test_the_plan_is_renamed_and_its_rule_changed(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->call('startEditing')
            ->set('name', 'Diploma')
            ->set('completionOperator', 'at_least_credits')
            ->set('requiredCredits', '12')
            ->set('isActive', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('isEditing', false)
            ->assertSee('Closed');

        $plan->refresh();
        $this->assertSame('Diploma', $plan->name);
        $this->assertTrue($plan->uses_credits);
        $this->assertSame(12, $plan->required_credits);
        $this->assertFalse($plan->is_active);
    }

    public function test_the_screen_says_how_far_a_learner_is(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan', 'read student']);
        $plan = $this->plan();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $requirement = $this->requirement($plan, $subject->id);
        $learner = $this->learner('Ada Bell');
        $this->publishResult($learner, $subject, 80);

        $this->get(route('graduation-plans.show', [$plan, 'student_record_id' => $learner->id]))
            ->assertOk()
            ->assertSee('Ada Bell')
            ->assertSee('Met')
            ->assertSee('80.00%');

        $this->assertSame(1, $requirement->fresh()->credits);
    }

    public function test_a_learner_below_the_pass_mark_has_not_met_the_requirement(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->requirement($plan, $subject->id);
        $learner = $this->learner('Ada Bell');
        $this->publishResult($learner, $subject, 30);

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->set('learnerId', (string) $learner->id)
            ->assertSee('Not met')
            ->assertSee('Still working through the plan');
    }

    public function test_a_learner_with_no_result_is_not_judged_against_it(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->requirement($plan, $subject->id);
        $learner = $this->learner('Ada Bell');

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->set('learnerId', (string) $learner->id)
            ->assertSee('No published result');
    }

    public function test_nobody_finishes_a_plan_that_asks_for_nothing(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $learner = $this->learner('Ada Bell');

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->set('learnerId', (string) $learner->id)
            ->assertSee('Still working through the plan')
            ->assertDontSee('Has finished the plan');

        $this->assertFalse(app(GraduationProgress::class)->isComplete($plan, $learner));
    }

    public function test_a_learner_is_excused_from_a_requirement(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $requirement = $this->requirement($plan, $subject->id);
        $learner = $this->learner('Ada Bell');

        $component = Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->set('learnerId', (string) $learner->id)
            ->call('excuse', $requirement->id)
            ->assertHasErrors('reasons.'.$requirement->id)
            ->set('reasons.'.$requirement->id, 'Passed the subject at another school.')
            ->call('excuse', $requirement->id)
            ->assertHasNoErrors()
            ->assertSee('Excused')
            ->assertSee('Passed the subject at another school.')
            ->assertSee('Has finished the plan');

        $exemption = GraduationExemption::sole();
        $this->assertTrue(AuditEvent::query()->where('action', AuditAction::GraduationExemptionChanged->value)->exists());

        $component->call('takeBack', $exemption->id);

        $this->assertSame(0, GraduationExemption::count());
    }

    public function test_a_requirement_of_another_plan_cannot_be_excused(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $otherPlan = $this->plan('Another plan');
        $requirement = $this->requirement($otherPlan, null);
        $learner = $this->learner('Ada Bell');
        $theirExemption = GraduationExemption::create([
            'graduation_requirement_id' => $requirement->id,
            'student_record_id' => $learner->id,
            'reason' => 'Kept.',
        ]);

        $this->assertThrows(
            fn () => Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
                ->set('learnerId', (string) $learner->id)
                ->set('reasons.'.$requirement->id, 'Should not work.')
                ->call('excuse', $requirement->id),
            ModelNotFoundException::class,
        );
        $this->assertThrows(
            fn () => Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])->call('takeBack', $theirExemption->id),
            ModelNotFoundException::class,
        );
        $this->assertThrows(
            fn () => Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])->call('removeRequirement', $requirement->id),
            ModelNotFoundException::class,
        );

        $this->assertSame(1, GraduationExemption::count());
        $this->assertNotNull($requirement->fresh());
    }

    public function test_a_learner_of_another_school_is_never_read(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $this->requirement($plan, null);
        $outsider = StudentRecord::factory()->create([
            'school_id' => School::factory()->create()->id,
            'user_id' => User::factory()->create(['name' => 'Their learner'])->id,
        ]);

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->set('learnerId', (string) $outsider->id)
            ->assertDontSee('Their learner')
            ->assertDontSee('Still working through the plan');
    }

    public function test_writing_a_plan_needs_its_own_permission(): void
    {
        $this->authorized_user(['read graduation plan']);
        $plan = $this->plan();

        Livewire::test(GraduationPlanRecord::class, ['plan' => $plan])
            ->assertDontSee('Add this requirement')
            ->set('requirementDescription', 'Pass mathematics')
            ->call('addRequirement')
            ->assertForbidden();

        Livewire::test(CreateGraduationPlanForm::class)->assertForbidden();

        $this->assertSame(0, $plan->requirements()->count());
    }

    public function test_the_screen_needs_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('graduation-plans.index'))->assertForbidden();
    }

    /**
     * Write a plan in the working school.
     */
    private function plan(string $name = 'Senior school diploma'): GraduationPlan
    {
        return GraduationPlan::create([
            'school_id' => $this->workingSchool()->id,
            'name' => $name,
            'uses_credits' => false,
        ]);
    }

    /**
     * Add one requirement to a plan.
     */
    private function requirement(GraduationPlan $plan, ?int $subjectId): GraduationRequirement
    {
        return GraduationRequirement::create([
            'graduation_plan_id' => $plan->id,
            'subject_id' => $subjectId,
            'description' => 'Pass mathematics',
            'credits' => 1,
            'pass_mark' => 50,
            'is_required' => true,
        ]);
    }

    /**
     * Enrol one named learner.
     */
    private function learner(string $name): StudentRecord
    {
        return StudentRecord::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'user_id' => User::factory()->create(['name' => $name])->id,
        ]);
    }

    /**
     * Publish one result for a learner in the given subject.
     */
    private function publishResult(StudentRecord $enrollment, Subject $subject, float $percentage): ResultSnapshot
    {
        $offering = CourseOffering::factory()->create([
            'school_id' => $enrollment->school_id,
            'subject_id' => $subject->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_period_id' => current_academic_period_id(),
        ]);

        return ResultSnapshot::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'course_offering_id' => $offering->id,
            'revision' => 1,
            'percentage' => $percentage,
            'payload' => [],
            'published_at' => now(),
        ]);
    }
}
