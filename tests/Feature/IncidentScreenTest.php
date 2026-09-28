<?php

namespace Tests\Feature;

use App\Actions\Discipline\ReportIncident;
use App\Actions\School\EndSchoolMembership;
use App\Enums\Feature;
use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Enums\IncidentStatus;
use App\Livewire\CreateIncident;
use App\Livewire\IncidentDirectory as IncidentDirectoryComponent;
use App\Livewire\ShowIncident;
use App\Models\Incident;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        $this->get(route('incidents.create'))->assertOk()->assertSeeLivewire(CreateIncident::class);

        $component = Livewire::test(CreateIncident::class)
            ->set('summary', 'Broke a window')
            ->set('category', IncidentCategory::Behaviour->value)
            ->set('description', 'The window in room four.')
            ->set('location', 'Room four')
            ->set('occurredAt', now()->subHour()->format('Y-m-d\TH:i'))
            ->set('participants.0.student_record_id', $enrollment->id)
            ->set('participants.0.note', 'Threw the ball')
            ->call('addParticipant')
            ->set('participants.1.role', IncidentParticipantRole::Witness->value)
            ->call('save')
            ->assertHasNoErrors();

        $incident = Incident::inSchool()->sole();

        $component->assertRedirect(route('incidents.show', $incident));

        $this->assertSame('Broke a window', $incident->summary);
        $this->assertSame(1, $incident->participants()->count());
        $this->assertSame('Threw the ball', $incident->participants()->sole()->note);
    }

    public function test_rows_are_added_and_removed_and_a_restricted_kind_is_marked(): void
    {
        $this->authorized_user(['read incident', 'create incident']);
        $restricted = collect(IncidentCategory::cases())->first(fn (IncidentCategory $category): bool => $category->isRestricted());

        $component = Livewire::test(CreateIncident::class)
            ->assertCount('participants', 1)
            ->call('addParticipant')
            ->call('addParticipant')
            ->assertCount('participants', 3)
            ->call('removeParticipant', 1)
            ->assertCount('participants', 2);

        if ($restricted !== null) {
            $component->set('category', $restricted->value)->assertSeeHtml('id="restricted-mark"');
        }
    }

    public function test_a_person_who_may_not_record_cases_cannot_open_the_form(): void
    {
        $this->authorized_user(['read incident']);

        Livewire::test(CreateIncident::class)->assertForbidden();
    }

    public function test_a_case_cannot_be_handed_to_somebody_who_left_the_school(): void
    {
        $this->authorized_user(['read incident', 'create incident']);
        $leaver = $this->memberOf($this->workingSchool());
        app(EndSchoolMembership::class)->end($leaver, $this->workingSchool());

        Livewire::test(CreateIncident::class)
            ->assertViewHas('staff', fn ($staff): bool => !$staff->contains('id', $leaver->id))
            ->set('summary', 'Broke a window')
            ->set('category', IncidentCategory::Behaviour->value)
            ->set('occurredAt', now()->subHour()->format('Y-m-d\TH:i'))
            ->set('assignedTo', $leaver->id)
            ->call('save')
            ->assertHasErrors('assignedTo');
    }

    public function test_a_case_cannot_be_recorded_in_the_future(): void
    {
        $this->authorized_user(['read incident', 'create incident']);

        Livewire::test(CreateIncident::class)
            ->set('summary', 'Something that has not happened')
            ->set('occurredAt', now()->addDay()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasErrors(['occurredAt' => 'before_or_equal']);

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

        Livewire::test(ShowIncident::class, ['incident' => $incident])
            ->set('nextStatus', IncidentStatus::UnderReview->value)
            ->set('statusReason', 'The head of year is looking into it.')
            ->call('changeStatus')
            ->assertHasNoErrors()
            ->assertSee('The head of year is looking into it.');

        $this->assertSame(IncidentStatus::UnderReview, $incident->fresh()->status);
        $this->assertSame(1, $incident->statusChanges()->count());
    }

    public function test_a_move_the_case_cannot_make_is_refused(): void
    {
        $this->authorized_user(['read incident', 'create incident', 'update incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');
        app(ReportIncident::class)->changeStatus($incident, IncidentStatus::Closed);

        Livewire::test(ShowIncident::class, ['incident' => $incident])
            ->assertDontSee('Move the case')
            ->set('nextStatus', IncidentStatus::Referred->value)
            ->call('changeStatus')
            ->assertHasErrors('nextStatus');

        $this->assertSame(IncidentStatus::Closed, $incident->fresh()->status);
    }

    public function test_an_action_is_added_and_marked_done(): void
    {
        $this->authorized_user(['read incident', 'create incident', 'update incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');

        $screen = Livewire::test(ShowIncident::class, ['incident' => $incident])
            ->set('actionType', 'Meeting')
            ->set('actionDescription', 'Speak to the guardian.')
            ->set('actionDueOn', now()->addWeek()->toDateString())
            ->call('addAction')
            ->assertHasNoErrors()
            ->assertSee('Speak to the guardian.')
            ->assertSet('actionType', '');

        $action = $incident->actions()->sole();

        $this->assertTrue($action->isOutstanding());

        $screen->call('completeAction', $action->id)->assertSee('Done '.now()->format('j M Y'));

        $this->assertFalse($action->fresh()->isOutstanding());
    }

    public function test_a_reader_without_update_permission_sees_no_forms(): void
    {
        $this->authorized_user(['read incident', 'create incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');

        Livewire::test(ShowIncident::class, ['incident' => $incident])
            ->assertDontSee('Move the case')
            ->assertDontSee('Add action')
            ->assertDontSee('Add note')
            ->call('addNote')
            ->assertForbidden();
    }

    public function test_an_action_needs_a_kind_and_what_has_to_happen(): void
    {
        $this->authorized_user(['read incident', 'create incident', 'update incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');

        Livewire::test(ShowIncident::class, ['incident' => $incident])
            ->call('addAction')
            ->assertHasErrors(['actionType' => 'required', 'actionDescription' => 'required']);

        $this->assertSame(0, $incident->actions()->count());
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

    public function test_case_filters_update_in_place_clear_and_ignore_invalid_values(): void
    {
        $this->authorized_user(['read incident', 'read safeguarding case', 'create incident', 'update incident']);
        $openBehaviour = app(ReportIncident::class)->report('Broke a window');
        $closedBehaviour = app(ReportIncident::class)->report('Late for class');
        app(ReportIncident::class)->changeStatus($closedBehaviour, IncidentStatus::Closed);
        $openSafeguarding = app(ReportIncident::class)->report('A welfare concern', IncidentCategory::Safeguarding);

        Livewire::test(IncidentDirectoryComponent::class)
            ->assertSee(route('incidents.show', $openBehaviour))
            ->assertSee(route('incidents.show', $closedBehaviour))
            ->assertSee(route('incidents.show', $openSafeguarding))
            ->set('category', IncidentCategory::Safeguarding->value)
            ->assertSee(route('incidents.show', $openSafeguarding))
            ->assertDontSee(route('incidents.show', $openBehaviour))
            ->set('status', IncidentStatus::Reported->value)
            ->assertSee(route('incidents.show', $openSafeguarding))
            ->set('openOnly', true)
            ->assertSee(route('incidents.show', $openSafeguarding))
            ->set('category', 'not-a-category')
            ->assertSet('category', '')
            ->assertSee(route('incidents.show', $openBehaviour))
            ->set('openOnly', false)
            ->set('status', 'not-a-status')
            ->assertSet('status', '')
            ->assertSee(route('incidents.show', $closedBehaviour))
            ->call('clearFilters')
            ->assertSet('category', '')
            ->assertSet('status', '')
            ->assertSet('openOnly', false)
            ->assertSee(route('incidents.show', $openBehaviour))
            ->assertSee(route('incidents.show', $closedBehaviour))
            ->assertSee(route('incidents.show', $openSafeguarding));
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
