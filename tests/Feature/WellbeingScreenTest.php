<?php

namespace Tests\Feature;

use App\Actions\Wellbeing\ManageSupportPlan;
use App\Enums\Feature;
use App\Enums\SupportCategory;
use App\Enums\SupportPlanStatus;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Models\User;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The wellbeing screens open a plan of help, run it, and keep health facts
 * away from people who may only read a student profile.
 */
class WellbeingScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_plan_list_starts_empty(): void
    {
        $this->authorized_user(['read support plan', 'create support plan']);

        $this->get(route('support-plans.index'))
            ->assertOk()
            ->assertSee('No support plans yet')
            ->assertSee(route('support-plans.create'));
    }

    public function test_a_plan_is_opened_from_the_screen(): void
    {
        $this->authorized_user(['read support plan', 'create support plan']);
        $enrollment = $this->enrollment();

        $response = $this->post(route('support-plans.store'), [
            'student_record_id' => $enrollment->id,
            'title' => 'Extra reading, four mornings a week',
            'category' => SupportCategory::Intervention->value,
            'summary' => 'The child reads two years below the class.',
            'starts_on' => now()->toDateString(),
            'review_on' => now()->addMonth()->toDateString(),
        ]);

        $plan = SupportPlan::inSchool()->sole();

        $response->assertRedirect(route('support-plans.show', $plan));

        $this->assertSame('Extra reading, four mornings a week', $plan->title);
        $this->assertSame(SupportPlanStatus::Draft, $plan->status);
        $this->assertFalse($plan->is_confidential);
    }

    public function test_a_plan_cannot_be_reviewed_before_it_starts(): void
    {
        $this->authorized_user(['read support plan', 'create support plan']);
        $enrollment = $this->enrollment();

        $this->post(route('support-plans.store'), [
            'student_record_id' => $enrollment->id,
            'title' => 'Extra reading',
            'category' => SupportCategory::Intervention->value,
            'starts_on' => now()->toDateString(),
            'review_on' => now()->subWeek()->toDateString(),
        ])->assertSessionHasErrors('review_on');

        $this->assertSame(0, SupportPlan::inSchool()->count());
    }

    public function test_the_plan_page_carries_the_steps_and_the_notes(): void
    {
        $this->authorized_user(['read support plan', 'create support plan', 'update support plan']);
        $plan = $this->plan();

        $this->from(route('support-plans.show', $plan))
            ->post(route('support-plans.actions.store', $plan), [
                'description' => 'Read with the learner every Tuesday.',
            ])
            ->assertRedirect(route('support-plans.show', $plan));

        $this->from(route('support-plans.show', $plan))
            ->post(route('support-plans.notes.store', $plan), ['body' => 'The learner read a page alone.'])
            ->assertRedirect(route('support-plans.show', $plan));

        $this->get(route('support-plans.show', $plan))
            ->assertOk()
            ->assertSee('Read with the learner every Tuesday.')
            ->assertSee('The learner read a page alone.');
    }

    public function test_a_step_is_marked_done_from_the_screen(): void
    {
        $this->authorized_user(['read support plan', 'create support plan', 'update support plan']);
        $plan = $this->plan();
        $action = app(ManageSupportPlan::class)->addAction($plan, 'Read with the learner every Tuesday.');

        $this->from(route('support-plans.show', $plan))
            ->post(route('support-plans.actions.complete', [$plan, $action]))
            ->assertRedirect(route('support-plans.show', $plan));

        $this->assertNotNull($action->fresh()->completed_at);
    }

    public function test_the_plan_moves_from_the_screen(): void
    {
        $this->authorized_user(['read support plan', 'create support plan', 'update support plan']);
        $plan = $this->plan();

        $this->from(route('support-plans.show', $plan))
            ->put(route('support-plans.status.update', $plan), [
                'status' => SupportPlanStatus::Active->value,
                'reason' => 'The guardian agreed.',
            ])
            ->assertRedirect(route('support-plans.show', $plan));

        $this->assertSame(SupportPlanStatus::Active, $plan->fresh()->status);
        $this->assertSame(1, $plan->statusChanges()->count());
    }

    public function test_a_move_the_plan_cannot_make_is_refused(): void
    {
        $this->authorized_user(['read support plan', 'create support plan', 'update support plan']);
        $plan = $this->plan();

        $this->from(route('support-plans.show', $plan))
            ->put(route('support-plans.status.update', $plan), ['status' => SupportPlanStatus::OnHold->value])
            ->assertSessionHasErrors('status');

        $this->assertSame(SupportPlanStatus::Draft, $plan->fresh()->status);
    }

    public function test_the_list_hides_a_confidential_plan_from_a_person_who_may_not_read_it(): void
    {
        $this->authorized_user(['read confidential support plan', 'create support plan']);
        $confidential = $this->plan(SupportCategory::Counselling);

        $this->authorized_user(['read support plan', 'create support plan']);
        $ordinary = $this->plan();

        $this->get(route('support-plans.index'))
            ->assertOk()
            ->assertSee(route('support-plans.show', $ordinary))
            ->assertDontSee(route('support-plans.show', $confidential));

        $this->get(route('support-plans.show', $confidential))->assertForbidden();
    }

    public function test_the_list_can_be_narrowed_to_the_plans_due_for_review(): void
    {
        $this->authorized_user(['read support plan', 'create support plan']);
        $manager = app(ManageSupportPlan::class);
        $due = $manager->open($this->enrollment(), 'Overdue plan', reviewOn: now()->subWeek());
        $later = $manager->open($this->enrollment(), 'Later plan', reviewOn: now()->addMonth());

        $this->get(route('support-plans.index', ['due' => 1]))
            ->assertOk()
            ->assertSee(route('support-plans.show', $due))
            ->assertDontSee(route('support-plans.show', $later));
    }

    public function test_the_health_screen_counts_the_learners_without_a_record(): void
    {
        $this->authorized_user(['read health record', 'update health record']);
        $this->enrollment();

        $this->get(route('health-records.index'))
            ->assertOk()
            ->assertSee('Without one')
            ->assertSee('Nothing held');
    }

    public function test_a_health_record_is_written_from_the_screen(): void
    {
        $this->authorized_user(['read health record', 'update health record']);
        $enrollment = $this->enrollment();

        $this->put(route('health-records.update', $enrollment), [
            'blood_group' => 'O+',
            'allergies' => 'Peanuts. Carry the pen.',
            'emergency_contact_name' => 'Ada Bell',
            'emergency_contact_phone' => '08000000000',
        ])->assertRedirect(route('health-records.edit', $enrollment));

        $record = StudentHealthRecord::inSchool()->sole();

        $this->assertSame('O+', $record->blood_group);
        $this->assertSame('Peanuts. Carry the pen.', $record->allergies);

        $this->get(route('health-records.edit', $enrollment))
            ->assertOk()
            ->assertSee('Peanuts. Carry the pen.');
    }

    public function test_a_health_record_validation_error_is_shown_on_the_screen(): void
    {
        $this->authorized_user(['read health record', 'update health record']);
        $enrollment = $this->enrollment();

        $this->put(route('health-records.update', $enrollment), [
            'notes' => 'Keep the inhaler in the front office.',
        ])->assertRedirect(route('health-records.edit', $enrollment));

        $response = $this->from(route('health-records.edit', $enrollment))
            ->put(route('health-records.update', $enrollment), [
                'notes' => str_repeat('x', 5001),
            ]);

        $response
            ->assertSessionHasErrors('notes')
            ->assertRedirect(route('health-records.edit', $enrollment));

        $this->get(route('health-records.edit', $enrollment))
            ->assertSee('The health record was not saved')
            ->assertSee('The notes must not be greater than 5000 characters.')
            ->assertSee('Keep the inhaler in the front office.');

        $this->assertSame(
            'Keep the inhaler in the front office.',
            StudentHealthRecord::inSchool()->sole()->notes,
        );
    }

    public function test_a_person_who_may_only_read_never_sees_the_save_button(): void
    {
        $this->authorized_user(['read health record']);
        $enrollment = $this->enrollment();

        $this->get(route('health-records.edit', $enrollment))
            ->assertOk()
            ->assertSee('You may read this record but not change it.')
            ->assertDontSee('Save the record');
    }

    public function test_reading_a_student_never_opens_the_health_record(): void
    {
        $this->authorized_user(['read student']);
        $enrollment = $this->enrollment();

        $this->get(route('health-records.index'))->assertForbidden();
        $this->get(route('health-records.edit', $enrollment))->assertForbidden();
    }

    public function test_a_school_that_turned_wellbeing_off_has_no_screens(): void
    {
        $this->authorized_user(['read support plan', 'read health record']);
        app(FeatureManager::class)->disable(Feature::Wellbeing);

        $this->get(route('support-plans.index'))->assertNotFound();
        $this->get(route('health-records.index'))->assertNotFound();
    }

    /**
     * Make an enrollment in the working school.
     */
    private function enrollment(?User $user = null): StudentRecord
    {
        return StudentRecord::factory()->create([
            'school_id' => $this->workingSchool()->id,
            ...($user === null ? [] : ['user_id' => $user->id]),
        ]);
    }

    /**
     * Open a plan for a new learner.
     */
    private function plan(SupportCategory $category = SupportCategory::Intervention): SupportPlan
    {
        return app(ManageSupportPlan::class)->open(
            enrollment: $this->enrollment(),
            title: 'Extra reading, four mornings a week',
            category: $category,
        );
    }
}
