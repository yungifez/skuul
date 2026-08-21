<?php

namespace App\Actions\Discipline;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Enums\IncidentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Incident;
use App\Models\IncidentAction;
use App\Models\IncidentParticipant;
use App\Models\IncidentStatusChange;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Write a case down and keep it moving.
 *
 * A behaviour record and a safeguarding concern follow the same path. What
 * changes is who may read them, which the case says for itself.
 */
class ReportIncident
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Record a new case.
     *
     * @param array<int, array{user?: User|int|null, enrollment?: StudentRecord|int|null, role?: IncidentParticipantRole, note?: string|null}> $participants
     *
     * @throws InvalidValueException when the case is in the future
     */
    public function report(
        string $summary,
        IncidentCategory $category = IncidentCategory::Behaviour,
        ?string $description = null,
        CarbonInterface|string|null $occurredAt = null,
        array $participants = [],
        ?User $reporter = null,
        ?User $assignee = null,
        ?string $location = null,
    ): Incident {
        $when = Carbon::parse($occurredAt ?? now());

        if ($when->isFuture()) {
            throw new InvalidValueException('A case cannot be recorded for a time that has not happened.');
        }

        return DB::transaction(function () use ($summary, $category, $description, $when, $participants, $reporter, $assignee, $location): Incident {
            $incident = Incident::create([
                'school_id'        => current_school_id(),
                'reference'        => $this->reference(),
                'category'         => $category,
                'summary'          => $summary,
                'description'      => $description,
                'location'         => $location,
                'occurred_at'      => $when,
                'academic_year_id' => current_academic_year_id(),
                'semester_id'      => current_semester_id(),
                'reported_by'      => $reporter === null ? auth()->id() : $reporter->id,
                'assigned_to'      => $assignee?->id,
            ]);

            foreach ($participants as $participant) {
                $user = $participant['user'] ?? null;
                $enrollment = $participant['enrollment'] ?? null;

                IncidentParticipant::create([
                    'incident_id'       => $incident->id,
                    'user_id'           => $user instanceof User ? $user->id : $user,
                    'student_record_id' => $enrollment instanceof StudentRecord ? $enrollment->id : $enrollment,
                    'role'              => $participant['role'] ?? IncidentParticipantRole::Subject,
                    'note'              => $participant['note'] ?? null,
                ]);
            }

            $this->auditor->record(
                AuditAction::IncidentReported,
                $incident,
                ['category' => $category->value, 'reference' => $incident->reference],
                $reporter,
            );

            return $incident;
        });
    }

    /**
     * Move the case to another state.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function changeStatus(Incident $incident, IncidentStatus $status, ?User $actor = null, ?string $reason = null): Incident
    {
        $current = $incident->status;

        if ($current === $status) {
            return $incident;
        }

        if (!$current->canMoveTo($status)) {
            throw new InvalidValueException("A case cannot move from {$current->value} to {$status->value}.");
        }

        return DB::transaction(function () use ($incident, $current, $status, $actor, $reason): Incident {
            $incident->status = $status;
            $incident->save();

            IncidentStatusChange::create([
                'incident_id' => $incident->id,
                'from_status' => $current,
                'to_status'   => $status,
                'reason'      => $reason,
                'changed_by'  => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::IncidentStatusChanged,
                $incident,
                ['from' => $current->value, 'to' => $status->value, 'reason' => $reason],
                $actor,
            );

            return $incident;
        });
    }

    /**
     * Record something the school will do about the case.
     *
     * @throws InvalidValueException when the case is finished
     */
    public function addAction(
        Incident $incident,
        string $type,
        string $description,
        CarbonInterface|string|null $dueOn = null,
        ?User $assignee = null,
        ?User $actor = null,
    ): IncidentAction {
        if (!$incident->status->isOpen()) {
            throw new InvalidValueException('This case is finished. Reopen it before you add an action.');
        }

        return IncidentAction::create([
            'incident_id' => $incident->id,
            'type'        => $type,
            'description' => $description,
            'due_on'      => $dueOn === null ? null : Carbon::parse($dueOn),
            'assigned_to' => $assignee?->id,
            'created_by'  => $actor === null ? auth()->id() : $actor->id,
        ]);
    }

    /**
     * Build a reference people can quote.
     */
    private function reference(): string
    {
        do {
            $reference = 'CASE-'.now()->format('y').'-'.Str::upper(Str::random(6));
        } while (Incident::where('school_id', current_school_id())->where('reference', $reference)->exists());

        return $reference;
    }
}
