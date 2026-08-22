<?php

namespace Tests\Feature;

use App\Actions\Portal\SubmitPortalRequest;
use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Models\PortalRequest;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Ask the school for something')
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
            ->assertRedirect(route('portal-requests.index'));

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
    private function request(StudentRecord $enrollment, string $subject = 'A copy of the result slip'): PortalRequest
    {
        return app(SubmitPortalRequest::class)->submit($enrollment, $subject, person: $enrollment->user);
    }
}
