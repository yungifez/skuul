<?php

namespace Tests\Feature;

use App\Actions\Discipline\ReportIncident;
use App\Enums\Feature;
use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The case screens record what happened, move a case on, and keep a
 * safeguarding case away from people who may not read it.
 */
class IncidentScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_list_starts_empty(): void
    {
        $this->authorized_user(['read incident', 'create incident']);

        $this->get(route('incidents.index'))
            ->assertOk()
            ->assertSee('No cases yet')
            ->assertSee(route('incidents.create'));
    }

    public function test_a_case_is_recorded_from_the_screen(): void
    {
        $this->authorized_user(['read incident', 'create incident']);
        $enrollment = $this->enrollment();

        $response = $this->post(route('incidents.store'), [
            'summary' => 'Broke a window',
            'category' => IncidentCategory::Behaviour->value,
            'description' => 'The window in room four.',
            'location' => 'Room four',
            'occurred_at' => now()->subHour()->format('Y-m-d\TH:i'),
            'participants' => [
                ['student_record_id' => $enrollment->id, 'role' => IncidentParticipantRole::Subject->value, 'note' => 'Threw the ball'],
                ['student_record_id' => '', 'role' => IncidentParticipantRole::Witness->value, 'note' => ''],
            ],
        ]);

        $incident = Incident::inSchool()->sole();

        $response->assertRedirect(route('incidents.show', $incident));

        $this->assertSame('Broke a window', $incident->summary);
        $this->assertSame(1, $incident->participants()->count());
        $this->assertSame('Threw the ball', $incident->participants()->sole()->note);
    }

    public function test_a_case_cannot_be_recorded_in_the_future(): void
    {
        $this->authorized_user(['read incident', 'create incident']);

        $this->post(route('incidents.store'), [
            'summary' => 'Something that has not happened',
            'category' => IncidentCategory::Behaviour->value,
            'occurred_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors('occurred_at');

        $this->assertSame(0, Incident::inSchool()->count());
    }

    public function test_the_case_page_shows_the_people_and_the_history(): void
    {
        $this->authorized_user(['read incident', 'create incident', 'update incident']);
        $enrollment = $this->enrollment(User::factory()->create(['name' => 'Ada Bell']));

        $incident = app(ReportIncident::class)->report(
            summary: 'Broke a window',
            participants: [['enrollment' => $enrollment, 'role' => IncidentParticipantRole::Subject]],
        );

        $this->get(route('incidents.show', $incident))
            ->assertOk()
            ->assertSee($incident->reference)
            ->assertSee('Ada Bell')
            ->assertSee('Subject of the case')
            ->assertSee('Reported');
    }

    public function test_the_case_moves_from_the_screen(): void
    {
        $this->authorized_user(['read incident', 'create incident', 'update incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');

        $this->from(route('incidents.show', $incident))
            ->put(route('incidents.status.update', $incident), [
                'status' => IncidentStatus::UnderReview->value,
                'reason' => 'The head of year is looking into it.',
            ])
            ->assertRedirect(route('incidents.show', $incident));

        $this->assertSame(IncidentStatus::UnderReview, $incident->fresh()->status);
        $this->assertSame(1, $incident->statusChanges()->count());
    }

    public function test_a_move_the_case_cannot_make_is_refused(): void
    {
        $this->authorized_user(['read incident', 'create incident', 'update incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');
        app(ReportIncident::class)->changeStatus($incident, IncidentStatus::Closed);

        $this->from(route('incidents.show', $incident))
            ->put(route('incidents.status.update', $incident), ['status' => IncidentStatus::Referred->value])
            ->assertSessionHasErrors('status');

        $this->assertSame(IncidentStatus::Closed, $incident->fresh()->status);
    }

    public function test_an_action_is_added_and_marked_done(): void
    {
        $this->authorized_user(['read incident', 'create incident', 'update incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');

        $this->from(route('incidents.show', $incident))
            ->post(route('incidents.actions.store', $incident), [
                'type' => 'Meeting',
                'description' => 'Speak to the guardian.',
                'due_on' => now()->addWeek()->toDateString(),
            ])
            ->assertRedirect(route('incidents.show', $incident));

        $action = $incident->actions()->sole();

        $this->assertTrue($action->isOutstanding());

        $this->from(route('incidents.show', $incident))
            ->post(route('incidents.actions.complete', [$incident, $action]))
            ->assertRedirect(route('incidents.show', $incident));

        $this->assertFalse($action->fresh()->isOutstanding());
    }

    public function test_the_list_hides_a_safeguarding_case_from_a_person_who_may_not_read_it(): void
    {
        $this->authorized_user(['read safeguarding case', 'create incident']);
        $restricted = app(ReportIncident::class)->report('A concern about a child', IncidentCategory::Safeguarding);

        $this->authorized_user(['read incident', 'create incident']);
        $ordinary = app(ReportIncident::class)->report('Broke a window');

        $this->get(route('incidents.index'))
            ->assertOk()
            ->assertSee(route('incidents.show', $ordinary))
            ->assertDontSee(route('incidents.show', $restricted));

        $this->get(route('incidents.show', $restricted))->assertForbidden();
    }

    public function test_the_list_can_be_narrowed_to_the_cases_that_need_work(): void
    {
        $this->authorized_user(['read incident', 'create incident', 'update incident']);
        $open = app(ReportIncident::class)->report('Broke a window');
        $closed = app(ReportIncident::class)->report('Late for class');
        app(ReportIncident::class)->changeStatus($closed, IncidentStatus::Closed);

        $this->get(route('incidents.index', ['open' => 1]))
            ->assertOk()
            ->assertSee(route('incidents.show', $open))
            ->assertDontSee(route('incidents.show', $closed));
    }

    public function test_the_screen_needs_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('incidents.index'))->assertForbidden();
    }

    public function test_a_school_that_turned_discipline_off_has_no_screen(): void
    {
        $this->authorized_user(['read incident']);
        app(FeatureManager::class)->disable(Feature::Discipline);

        $this->get(route('incidents.index'))->assertNotFound();
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
}
