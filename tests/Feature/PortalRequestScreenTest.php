<?php

namespace Tests\Feature;

use App\Actions\Portal\SubmitPortalRequest;
use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Livewire\PortalRequestInbox;
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

        $this->actingAs($guardian)
            ->from(route('portal.requests.index', $enrollment))
            ->post(route('portal.requests.store', $enrollment), [
                'subject' => 'A copy of the result slip',
                'type' => PortalRequestType::Document->value,
                'message' => 'For a visa application.',
            ])
            ->assertRedirect(route('portal.requests.index', $enrollment));

        $request = PortalRequest::sole();

        $this->assertSame($guardian->id, $request->requested_by);
        $this->assertSame(PortalRequestStatus::Submitted, $request->status);

        $this->actingAs($guardian)
            ->get(route('portal.requests.index', $enrollment))
            ->assertOk()
            ->assertSee('A copy of the result slip')
            ->assertSee('Sent');
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

        $this->from(route('portal-requests.index'))
            ->put(route('portal-requests.status.update', $request), [
                'status' => PortalRequestStatus::Answered->value,
                'response' => 'The slip is ready at the office.',
            ])
            ->assertRedirect(route('portal-requests.index'))
            ->assertSessionHas('success', 'Request status updated to answered.');

        $this->assertSame(PortalRequestStatus::Answered, $request->fresh()->status);
        $this->assertSame('The slip is ready at the office.', $request->fresh()->response);
        $this->assertNotNull($request->fresh()->answered_at);
    }

    public function test_an_answer_must_carry_the_answer(): void
    {
        $enrollment = $this->enrollment();
        $request = $this->request($enrollment);
        $this->authorized_user(['read portal request', 'answer portal request']);

        $this->from(route('portal-requests.index'))
            ->put(route('portal-requests.status.update', $request), [
                'status' => PortalRequestStatus::Answered->value,
            ])
            ->assertSessionHasErrors('response');

        $this->assertSame(PortalRequestStatus::Submitted, $request->fresh()->status);

        $this->get(route('portal-requests.index'))
            ->assertSee('An answered request must carry the answer.');
    }

    public function test_a_family_never_answers_its_own_request(): void
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

        $this->actingAs($guardian->refresh())
            ->from(route('portal-requests.index'))
            ->put(route('portal-requests.status.update', $request), [
                'status' => PortalRequestStatus::Answered->value,
                'response' => 'I answer myself.',
            ])
            ->assertForbidden();

        $this->assertSame(PortalRequestStatus::Submitted, $request->fresh()->status);
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
