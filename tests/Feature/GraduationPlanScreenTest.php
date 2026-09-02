<?php

namespace Tests\Feature;

use App\Models\CourseOffering;
use App\Models\GraduationExemption;
use App\Models\GraduationPlan;
use App\Models\GraduationRequirement;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Start with the basics')
            ->assertSee('Every item is required by default')
            ->assertSee('Advanced rules (optional)')
            ->assertDontSee('mt-6 rounded-md border p-4" open');
    }

    public function test_a_plan_is_written_from_the_screen(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);

        $response = $this->post(route('graduation-plans.store'), [
            'name' => 'Senior school diploma',
            'uses_credits' => '1',
            'required_credits' => '24',
        ]);

        $plan = GraduationPlan::inSchool()->sole();

        $response->assertRedirect(route('graduation-plans.show', $plan));

        $this->assertTrue($plan->uses_credits);
        $this->assertSame(24, $plan->required_credits);
    }

    public function test_a_plan_that_counts_credits_must_say_how_many(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);

        $this->post(route('graduation-plans.store'), [
            'name' => 'Senior school diploma',
            'uses_credits' => '1',
        ])->assertSessionHasErrors('required_credits');

        $this->assertSame(0, GraduationPlan::inSchool()->count());
    }

    public function test_a_requirement_is_added_and_removed(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);

        $this->from(route('graduation-plans.show', $plan))
            ->post(route('graduation-plans.requirements.store', $plan), [
                'description' => 'Pass mathematics',
                'subject_id' => $subject->id,
                'pass_mark' => '50',
                'credits' => '3',
                'is_required' => '1',
            ])
            ->assertRedirect(route('graduation-plans.show', $plan));

        $requirement = $plan->requirements()->sole();

        $this->get(route('graduation-plans.show', $plan))
            ->assertOk()
            ->assertSee('Pass mathematics')
            ->assertSee('Must be met');

        $this->from(route('graduation-plans.show', $plan))
            ->delete(route('graduation-plans.requirements.destroy', [$plan, $requirement]))
            ->assertRedirect(route('graduation-plans.show', $plan));

        $this->assertSame(0, $plan->requirements()->count());
    }

    public function test_a_plan_can_have_ordered_nested_stages_and_logic(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();

        $this->from(route('graduation-plans.show', $plan))
            ->post(route('graduation-plans.children.store', $plan), [
                'name' => 'KG 1',
                'completion_operator' => 'all',
                'position' => 1,
                'is_negated' => '0',
            ])->assertRedirect(route('graduation-plans.show', $plan));

        $stage = $plan->children()->sole();

        $this->post(route('graduation-plans.children.store', $plan), [
            'name' => 'Later kindergarten',
            'completion_operator' => 'any',
            'position' => 2,
            'is_negated' => '0',
        ]);

        $choice = $plan->children()->where('name', 'Later kindergarten')->sole();

        $this->from(route('graduation-plans.show', $plan))
            ->post(route('graduation-plans.children.store', $plan), [
                'name' => 'Electives',
                'completion_operator' => 'at_least',
                'required_count' => 4,
                'is_negated' => '0',
            ])->assertRedirect(route('graduation-plans.show', $plan));

        $this->from(route('graduation-plans.show', $choice))
            ->post(route('graduation-plans.children.store', $choice), [
                'name' => 'KG 2',
                'completion_operator' => 'all',
                'is_negated' => '0',
            ])->assertRedirect(route('graduation-plans.show', $choice));

        $this->get(route('graduation-plans.show', $plan))
            ->assertOk()
            ->assertSee('KG 1')
            ->assertSee('Later kindergarten')
            ->assertSee('KG 2')
            ->assertSee('Any item (OR)')
            ->assertSee('At least 4 items');

        $this->assertSame($plan->id, $stage->fresh()->parent_id);
        $this->assertSame($choice->id, $choice->children()->sole()->parent_id);
    }

    public function test_a_stage_can_require_credits_from_its_subjects(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();

        $this->from(route('graduation-plans.show', $plan))
            ->post(route('graduation-plans.children.store', $plan), [
                'name' => 'Science credits',
                'completion_operator' => 'at_least_credits',
                'required_credits' => 3,
                'is_negated' => '0',
            ])
            ->assertRedirect(route('graduation-plans.show', $plan));

        $stage = $plan->children()->sole();

        $this->assertTrue($stage->uses_credits);
        $this->assertSame(3, $stage->required_credits);

        $this->get(route('graduation-plans.show', $plan))
            ->assertOk()
            ->assertSee('Require a number of credits')
            ->assertSee('At least 3 credits');
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

        $this->get(route('graduation-plans.show', [$plan, 'student_record_id' => $learner->id]))
            ->assertOk()
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

        $this->get(route('graduation-plans.show', [$plan, 'student_record_id' => $learner->id]))
            ->assertOk()
            ->assertSee('No published result');
    }

    public function test_a_learner_is_excused_from_a_requirement(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $requirement = $this->requirement($plan, $subject->id);
        $learner = $this->learner('Ada Bell');

        $this->from(route('graduation-plans.show', $plan))
            ->post(route('graduation-plans.exemptions.store', $plan), [
                'graduation_requirement_id' => $requirement->id,
                'student_record_id' => $learner->id,
                'reason' => 'Passed the subject at another school.',
            ])
            ->assertRedirect(route('graduation-plans.show', $plan));

        $this->get(route('graduation-plans.show', [$plan, 'student_record_id' => $learner->id]))
            ->assertOk()
            ->assertSee('Excused')
            ->assertSee('Passed the subject at another school.')
            ->assertSee('Has finished the plan');

        $exemption = GraduationExemption::sole();

        $this->from(route('graduation-plans.show', $plan))
            ->delete(route('graduation-plans.exemptions.destroy', [$plan, $exemption]))
            ->assertRedirect(route('graduation-plans.show', $plan));

        $this->assertSame(0, GraduationExemption::count());
    }

    public function test_a_requirement_of_another_plan_cannot_be_excused(): void
    {
        $this->authorized_user(['read graduation plan', 'manage graduation plan']);
        $plan = $this->plan();
        $otherPlan = $this->plan('Another plan');
        $requirement = $this->requirement($otherPlan, null);
        $learner = $this->learner('Ada Bell');

        $this->from(route('graduation-plans.show', $plan))
            ->post(route('graduation-plans.exemptions.store', $plan), [
                'graduation_requirement_id' => $requirement->id,
                'student_record_id' => $learner->id,
                'reason' => 'Should not work.',
            ])
            ->assertSessionHasErrors('graduation_requirement_id');

        $this->assertSame(0, GraduationExemption::count());
    }

    public function test_writing_a_plan_needs_its_own_permission(): void
    {
        $this->authorized_user(['read graduation plan']);
        $plan = $this->plan();

        $this->get(route('graduation-plans.show', $plan))->assertOk()->assertDontSee('Add this requirement');

        $this->post(route('graduation-plans.requirements.store', $plan), [
            'description' => 'Pass mathematics',
            'pass_mark' => '50',
            'credits' => '1',
            'is_required' => '1',
        ])->assertForbidden();
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
