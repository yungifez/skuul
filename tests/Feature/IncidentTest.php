<?php

namespace Tests\Feature;

use App\Actions\Discipline\ReportIncident;
use App\Enums\AuditAction;
use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Enums\IncidentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\Incident;
use App\Models\School;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use Tests\TestCase;

/**
 * Cases are recorded, moved, and read only by the people who should read them.
 */
class IncidentTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_case_is_recorded_with_the_people_in_it(): void
    {
        $this->authorized_user(['create incident']);
        $enrollment = $this->enrollment();

        $incident = app(ReportIncident::class)->report(
            summary: 'Broke a window',
            description: 'The window in room four.',
            participants: [
                ['enrollment' => $enrollment, 'role' => IncidentParticipantRole::Subject],
            ],
            location: 'Room four',
        );

        $this->assertSame(IncidentStatus::Reported, $incident->status);
        $this->assertSame(IncidentCategory::Behaviour, $incident->category);
        $this->assertFalse($incident->is_restricted);
        $this->assertStringStartsWith('CASE-', $incident->reference);
        $this->assertSame(1, $incident->participants()->count());
        $this->assertSame($enrollment->id, $incident->participants()->firstOrFail()->student_record_id);
    }

    public function test_a_safeguarding_case_is_restricted_by_itself(): void
    {
        $this->authorized_user(['create incident']);

        $incident = app(ReportIncident::class)->report('A concern about a child', IncidentCategory::Safeguarding);

        $this->assertTrue($incident->is_restricted);
    }

    public function test_a_case_cannot_be_recorded_in_the_future(): void
    {
        $this->authorized_user(['create incident']);

        $this->expectException(InvalidValueException::class);

        app(ReportIncident::class)->report('Something that has not happened', occurredAt: now()->addDay());
    }

    public function test_a_case_moves_through_its_states(): void
    {
        $this->authorized_user(['create incident']);
        $action = app(ReportIncident::class);
        $incident = $action->report('Late every morning');

        $action->changeStatus($incident, IncidentStatus::UnderReview);
        $action->changeStatus($incident, IncidentStatus::Resolved, reason: 'The family agreed a plan');

        $this->assertSame(IncidentStatus::Resolved, $incident->fresh()->status);
        $this->assertSame(2, $incident->statusChanges()->count());
        $this->assertSame('The family agreed a plan', $incident->statusChanges()->reorder('id', 'desc')->firstOrFail()->reason);
    }

    public function test_a_closed_case_stops_moving(): void
    {
        $this->authorized_user(['create incident']);
        $action = app(ReportIncident::class);
        $incident = $action->report('Late every morning');
        $action->changeStatus($incident, IncidentStatus::Closed);

        $this->expectException(InvalidValueException::class);

        $action->changeStatus($incident->fresh(), IncidentStatus::UnderReview);
    }

    public function test_case_history_cannot_be_changed(): void
    {
        $this->authorized_user(['create incident']);
        $action = app(ReportIncident::class);
        $incident = $action->report('Late every morning');
        $action->changeStatus($incident, IncidentStatus::UnderReview);
        $change = $incident->statusChanges()->firstOrFail();

        $this->expectException(RuntimeException::class);

        $change->update(['reason' => 'Something else']);
    }

    public function test_an_action_is_recorded_against_an_open_case(): void
    {
        $this->authorized_user(['create incident']);
        $reporter = app(ReportIncident::class);
        $incident = $reporter->report('Broke a window');

        $action = $reporter->addAction($incident, 'meeting', 'Meet the guardians', now()->addDays(3));

        $this->assertTrue($action->isOutstanding());
        $this->assertSame(1, $incident->actions()->count());

        $action->complete();

        $this->assertFalse($action->fresh()->isOutstanding());
    }

    public function test_a_finished_case_takes_no_new_action(): void
    {
        $this->authorized_user(['create incident']);
        $reporter = app(ReportIncident::class);
        $incident = $reporter->report('Broke a window');
        $reporter->changeStatus($incident, IncidentStatus::Resolved);

        $this->expectException(InvalidValueException::class);

        $reporter->addAction($incident->fresh(), 'meeting', 'Meet the guardians');
    }

    public function test_an_ordinary_case_needs_only_the_incident_permission(): void
    {
        $this->authorized_user(['create incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');

        $this->authorized_user(['read incident']);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $incident));
    }

    public function test_a_safeguarding_case_is_hidden_from_ordinary_readers(): void
    {
        $this->authorized_user(['create incident']);
        $incident = app(ReportIncident::class)->report('A concern about a child', IncidentCategory::Safeguarding);

        $this->authorized_user(['read incident']);
        $reader = auth()->user();

        $this->assertFalse(Gate::forUser($reader)->allows('view', $incident));
    }

    public function test_a_safeguarding_case_is_readable_by_its_handler(): void
    {
        $this->authorized_user(['read incident']);
        $handler = auth()->user();
        $incident = app(ReportIncident::class)->report(
            'A concern about a child',
            IncidentCategory::Safeguarding,
            assignee: $handler,
        );

        $this->assertTrue(Gate::forUser($handler->fresh())->allows('view', $incident));
    }

    public function test_a_safeguarding_case_is_readable_with_the_right_permission(): void
    {
        $this->authorized_user(['create incident']);
        $incident = app(ReportIncident::class)->report('A concern about a child', IncidentCategory::Safeguarding);

        $this->authorized_user(['read safeguarding case']);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $incident));
    }

    public function test_another_school_never_reads_the_case(): void
    {
        $this->authorized_user(['create incident']);
        $incident = app(ReportIncident::class)->report('Broke a window');

        $this->authorized_user(['read incident', 'read safeguarding case'], School::factory()->create());

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $incident));
    }

    public function test_the_readable_scope_hides_restricted_cases(): void
    {
        $this->authorized_user(['create incident']);
        $reporter = app(ReportIncident::class);
        $reporter->report('Broke a window');
        $reporter->report('A concern about a child', IncidentCategory::Safeguarding);

        $this->authorized_user(['read incident']);

        $this->assertSame(1, Incident::inSchool()->readableBy(auth()->user())->count());
    }

    public function test_recording_a_case_is_written_to_the_audit_log(): void
    {
        $this->authorized_user(['create incident']);

        $incident = app(ReportIncident::class)->report('Broke a window');

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::IncidentReported)->forSubject($incident)->first());
    }

    /**
     * Create an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }
}
