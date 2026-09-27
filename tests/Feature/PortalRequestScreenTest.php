<?php

namespace Tests\Feature;

use App\Actions\Portal\SubmitPortalRequest;
use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Livewire\PortalRequestInbox;
use App\Livewire\PortalRequests;
use App\Models\PortalRequest;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A family asks through the portal and the school answers in its inbox. A
 * request changes no school record by itself.
 */
class PortalRequestScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_family_sees_where_to_ask(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);

        $this->actingAs($guardian)
            ->get(route('portal.requests.index', $enrollment))
            ->assertOk()
            ->assertSee('Ask the school')
            ->assertSee('Send a message about '.$enrollment->user->name.'. The school will read and answer it.')
            ->assertDontSee('Ask the school for something')
            ->assertDontSee('Anything else the school should know')
            ->assertSee('You have not asked for anything yet');
    }

    public function test_a_family_sends_a_request(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        $this->actingAs($guardian);

        $this->get(route('portal.requests.index', $enrollment))->assertSeeLivewire(PortalRequests::class);

        Livewire::test(PortalRequests::class, ['studentRecord' => $enrollment])
            ->set('subject', 'A copy of the result slip')
            ->set('type', PortalRequestType::Document->value)
            ->set('message', 'For a visa application.')
            ->call('send')
            ->assertHasNoErrors()
            ->assertDispatched('status-message', type: 'success', message: 'Your request was sent to the school.')
            ->assertSet('subject', '')
            ->assertSee('A copy of the result slip')
            ->assertSee('Sent');

        $request = PortalRequest::sole();

        $this->assertSame($guardian->id, $request->requested_by);
        $this->assertSame(PortalRequestStatus::Submitted, $request->status);
    }

    public function test_pressing_send_twice_sends_one_request(): void
    {
        $enrollment = $this->enrollment();
        $this->actingAs($this->guardianOf($enrollment));

        Livewire::test(PortalRequests::class, ['studentRecord' => $enrollment])
            ->set('subject', 'A copy of the result slip')
            ->call('send')
            ->set('subject', 'A copy of the result slip')
            ->call('send')
            ->assertHasErrors('subject')
            ->assertSee('You already asked for this.');

        $this->assertSame(1, PortalRequest::query()->count());
    }

    public function test_a_calendar_link_fills_in_the_request(): void
    {
        $enrollment = $this->enrollment();
        $this->actingAs($this->guardianOf($enrollment));

        Livewire::withQueryParams([
            'type' => PortalRequestType::Appointment->value,
            'subject' => 'Appointment: Open day',
            'message' => 'I would like the published time.',
        ])->test(PortalRequests::class, ['studentRecord' => $enrollment])
            ->assertSet('type', PortalRequestType::Appointment->value)
            ->assertSet('subject', 'Appointment: Open day')
            ->assertSet('message', 'I would like the published time.');

        Livewire::withQueryParams(['type' => 'not-a-type'])
            ->test(PortalRequests::class, ['studentRecord' => $enrollment])
            ->assertSet('type', PortalRequestType::Document->value);
    }

    public function test_a_family_takes_back_an_open_request_only(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        $open = app(SubmitPortalRequest::class)->submit($enrollment, 'A copy of the result slip', person: $guardian);
        $answered = app(SubmitPortalRequest::class)->submit($enrollment, 'An appointment', person: $guardian);
        app(SubmitPortalRequest::class)->answer($answered, 'Come on Tuesday.', User::factory()->create());
        $this->actingAs($guardian);

        Livewire::test(PortalRequests::class, ['studentRecord' => $enrollment])
            ->call('withdraw', $open->id)
            ->assertDispatched('status-message', type: 'success', message: 'Request taken back.')
            ->call('withdraw', $answered->id)
            ->assertDispatched('status-message', type: 'danger', message: 'The school has already closed this request.');

        $this->assertSame(PortalRequestStatus::Cancelled, $open->fresh()->status);
        $this->assertSame(PortalRequestStatus::Answered, $answered->fresh()->status);
    }

    public function test_a_family_never_takes_back_somebody_elses_request(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        $learnersOwn = $this->request($enrollment);
        $this->actingAs($guardian);

        $this->assertThrows(
            fn () => Livewire::test(PortalRequests::class, ['studentRecord' => $enrollment])->call('withdraw', $learnersOwn->id),
            ModelNotFoundException::class,
        );

        $this->assertSame(PortalRequestStatus::Submitted, $learnersOwn->fresh()->status);
    }

    public function test_a_guardian_whose_link_ended_cannot_keep_asking(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        $this->actingAs($guardian);

        $screen = Livewire::test(PortalRequests::class, ['studentRecord' => $enrollment]);
        $guardian->parentRecord->students()->detach($enrollment->user);

        $screen->set('subject', 'A copy of the result slip')->call('send')->assertForbidden();

        $this->assertSame(0, PortalRequest::query()->count());
    }

    public function test_a_family_cannot_open_another_familys_screen_through_livewire(): void
    {
        $enrollment = $this->enrollment();
        $otherSchool = School::factory()->create();
        $foreignEnrollment = StudentRecord::factory()->create(['school_id' => $otherSchool->id]);
        $this->actingAs($this->guardianOf($enrollment));

        Livewire::test(PortalRequests::class, ['studentRecord' => $foreignEnrollment])->assertForbidden();
    }

    public function test_a_stranger_never_opens_the_request_screen(): void
    {
        $enrollment = $this->enrollment();
        $stranger = $this->memberOf($this->workingSchool());

        $this->actingAs($stranger)
            ->get(route('portal.requests.index', $enrollment))
            ->assertForbidden();
    }

    public function test_a_closed_requests_area_has_no_screen(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        features()->enable(Feature::Portal, config: [PortalArea::Requests->value => false]);

        $this->actingAs($guardian)
            ->get(route('portal.requests.index', $enrollment))
            ->assertNotFound();
    }

    public function test_the_school_inbox_starts_empty(): void
    {
        $this->authorized_user(['read portal request']);

        $this->get(route('portal-requests.index'))
            ->assertOk()
            ->assertSee('No requests yet');
    }

    public function test_the_school_answers_from_the_inbox(): void
    {
        $enrollment = $this->enrollment();
        $request = $this->request($enrollment);
        $this->authorized_user(['read portal request', 'answer portal request']);

        Livewire::test(PortalRequestInbox::class)
            ->set('statusesByRequest.'.$request->id, PortalRequestStatus::Answered->value)
            ->set('responsesByRequest.'.$request->id, 'The slip is ready at the office.')
            ->call('changeStatus', $request->id)
            ->assertHasNoErrors();

        $this->assertSame(PortalRequestStatus::Answered, $request->fresh()->status);
        $this->assertSame('The slip is ready at the office.', $request->fresh()->response);
        $this->assertNotNull($request->fresh()->answered_at);
        $this->assertSame(auth()->id(), $request->fresh()->answered_by);
    }

    public function test_the_school_cannot_answer_a_request_the_family_took_back(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        $request = app(SubmitPortalRequest::class)->submit($enrollment, 'A copy of the result slip', person: $guardian);
        app(SubmitPortalRequest::class)->withdraw($request, $guardian);
        $this->authorized_user(['read portal request', 'answer portal request']);

        Livewire::test(PortalRequestInbox::class)
            ->set('statusesByRequest.'.$request->id, PortalRequestStatus::Answered->value)
            ->set('responsesByRequest.'.$request->id, 'Too late.')
            ->call('changeStatus', $request->id)
            ->assertHasErrors('status');

        $this->assertSame(PortalRequestStatus::Cancelled, $request->fresh()->status);
    }

    public function test_the_family_reads_the_answer_in_the_portal(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        $request = app(SubmitPortalRequest::class)->submit(
            $enrollment,
            'A copy of the result slip',
            person: $guardian,
        );

        $this->authorized_user(['read portal request', 'answer portal request']);
        app(SubmitPortalRequest::class)->answer($request, 'The slip is ready at the office.', auth()->user());

        $this->actingAs($guardian)
            ->get(route('portal.requests.index', $enrollment))
            ->assertOk()
            ->assertSee('The slip is ready at the office.')
            ->assertSee('Answered');
    }

    public function test_the_inbox_filters_by_state(): void
    {
        $enrollment = $this->enrollment();
        $waiting = $this->request($enrollment, 'A copy of the result slip');
        $answered = $this->request($enrollment, 'An appointment with the head');
        $this->authorized_user(['read portal request', 'answer portal request']);
        app(SubmitPortalRequest::class)->answer($answered, 'Come on Tuesday.', auth()->user());

        $this->get(route('portal-requests.index', ['status' => PortalRequestStatus::Submitted->value]))
            ->assertOk()
            ->assertSee('A copy of the result slip')
            ->assertDontSee('An appointment with the head');
    }

    public function test_the_live_inbox_filters_requests_and_clears_filters(): void
    {
        $enrollment = $this->enrollment();
        $this->request($enrollment, 'A copy of the result slip');
        $appointment = $this->request(
            $enrollment,
            'An appointment with the head',
            PortalRequestType::Appointment,
        );
        $this->authorized_user(['read portal request', 'answer portal request']);
        app(SubmitPortalRequest::class)->answer($appointment, 'Come on Tuesday.', auth()->user());

        Livewire::test(PortalRequestInbox::class)
            ->set('type', PortalRequestType::Appointment->value)
            ->assertSee('An appointment with the head')
            ->assertDontSee('A copy of the result slip')
            ->set('type', PortalRequestType::Document->value)
            ->set('status', PortalRequestStatus::Submitted->value)
            ->assertSee('A copy of the result slip')
            ->assertDontSee('An appointment with the head')
            ->call('clearFilters')
            ->assertSee('A copy of the result slip')
            ->assertSee('An appointment with the head');
    }

    public function test_the_school_moves_a_request_through_review_and_answer_without_leaving_the_inbox(): void
    {
        $request = $this->request($this->enrollment());
        $this->authorized_user(['read portal request', 'answer portal request']);

        Livewire::test(PortalRequestInbox::class)
            ->set('statusesByRequest.'.$request->id, PortalRequestStatus::InReview->value)
            ->assertDontSee('id="request-'.$request->id.'-response"', escape: false)
            ->call('changeStatus', $request->id)
            ->assertHasNoErrors()
            ->assertSee('Request status updated to being looked at.')
            ->assertSee('Being looked at')
            ->set('statusesByRequest.'.$request->id, PortalRequestStatus::Answered->value)
            ->assertSee('id="request-'.$request->id.'-response"', escape: false)
            ->set('responsesByRequest.'.$request->id, 'The slip is ready at the office.')
            ->call('changeStatus', $request->id)
            ->assertHasNoErrors()
            ->assertSee('Request status updated to answered.')
            ->assertSee('The slip is ready at the office.');

        $this->assertSame(PortalRequestStatus::Answered, $request->fresh()->status);
        $this->assertSame('The slip is ready at the office.', $request->fresh()->response);
    }

    public function test_the_live_inbox_requires_an_answer_and_allows_a_decline_without_one(): void
    {
        $request = $this->request($this->enrollment());
        $this->authorized_user(['read portal request', 'answer portal request']);
        $responseField = 'responsesByRequest.'.$request->id;

        Livewire::test(PortalRequestInbox::class)
            ->set('statusesByRequest.'.$request->id, PortalRequestStatus::Answered->value)
            ->call('changeStatus', $request->id)
            ->assertHasErrors([$responseField => 'required_if']);

        $this->assertSame(PortalRequestStatus::Submitted, $request->fresh()->status);

        Livewire::test(PortalRequestInbox::class)
            ->set('statusesByRequest.'.$request->id, PortalRequestStatus::Declined->value)
            ->call('changeStatus', $request->id)
            ->assertHasNoErrors()
            ->assertSee('Declined');

        $this->assertSame(PortalRequestStatus::Declined, $request->fresh()->status);

        Livewire::test(PortalRequestInbox::class)
            ->set('statusesByRequest.'.$request->id, PortalRequestStatus::Answered->value)
            ->set('responsesByRequest.'.$request->id, 'An answer was already closed out.')
            ->call('changeStatus', $request->id)
            ->assertHasErrors('status')
            ->assertSee('A request cannot move from declined to answered.');

        $this->assertSame(PortalRequestStatus::Declined, $request->fresh()->status);
    }

    public function test_a_family_cannot_answer_its_own_request_through_livewire(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        $request = app(SubmitPortalRequest::class)->submit(
            $enrollment,
            'A copy of the result slip',
            person: $guardian,
        );

        school_context()->set($this->workingSchool(), remember: false);
        $guardian->givePermissionTo(['read portal request', 'answer portal request']);

        $this->actingAs($guardian->refresh());

        Livewire::test(PortalRequestInbox::class)
            ->set('statusesByRequest.'.$request->id, PortalRequestStatus::Answered->value)
            ->set('responsesByRequest.'.$request->id, 'I answer myself.')
            ->call('changeStatus', $request->id)
            ->assertForbidden();

        $this->assertSame(PortalRequestStatus::Submitted, $request->fresh()->status);
    }

    public function test_the_live_inbox_cannot_read_or_change_another_schools_request(): void
    {
        $homeSchool = $this->workingSchool();
        $otherSchool = School::factory()->create();
        $foreignEnrollment = StudentRecord::factory()->create(['school_id' => $otherSchool->id]);
        $foreignRequest = PortalRequest::create([
            'school_id' => $otherSchool->id,
            'student_record_id' => $foreignEnrollment->id,
            'requested_by' => User::factory()->create()->id,
            'type' => PortalRequestType::Document,
            'status' => PortalRequestStatus::Submitted,
            'subject' => 'Private request from another school',
        ]);
        $this->authorized_user(['read portal request', 'answer portal request'], $homeSchool);

        $inbox = Livewire::test(PortalRequestInbox::class)
            ->assertDontSee('Private request from another school')
            ->set('statusesByRequest.'.$foreignRequest->id, PortalRequestStatus::Answered->value)
            ->set('responsesByRequest.'.$foreignRequest->id, 'Attempted access');

        try {
            $inbox->call('changeStatus', $foreignRequest->id);
        } catch (ModelNotFoundException) {
            $this->assertSame(PortalRequestStatus::Submitted, $foreignRequest->fresh()->status);

            return;
        }

        $this->fail('The inbox action reached a request from another school.');
    }

    public function test_the_inbox_needs_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('portal-requests.index'))->assertForbidden();
    }

    /**
     * Make an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Make a guardian recorded against the student.
     */
    private function guardianOf(StudentRecord $enrollment): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        $guardian->parentRecord()->create(['user_id' => $guardian->id]);
        $guardian->refresh()->parentRecord->students()->syncWithoutDetaching($enrollment->user);

        return $guardian->fresh();
    }

    /**
     * Send one request as the learner.
     */
    private function request(
        StudentRecord $enrollment,
        string $subject = 'A copy of the result slip',
        PortalRequestType $type = PortalRequestType::Document,
    ): PortalRequest {
        return app(SubmitPortalRequest::class)->submit($enrollment, $subject, $type, person: $enrollment->user);
    }
}
