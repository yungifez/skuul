<?php

namespace App\Actions\Discipline;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\Incident;
use App\Models\IncidentNote;
use App\Models\User;

/**
 * Add one immutable note to an open case.
 */
class AddIncidentNote
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Add a note without allowing history to be edited later.
     *
     * @throws InvalidValueException when the case is finished
     */
    public function add(
        Incident $incident,
        string $body,
        bool $restricted = true,
        ?User $actor = null,
    ): IncidentNote {
        if (!$incident->status->isOpen()) {
            throw new InvalidValueException('This case is finished. Reopen it before you write a note.');
        }

        $note = IncidentNote::create([
            'school_id' => $incident->school_id,
            'incident_id' => $incident->id,
            'body' => $body,
            'is_restricted' => $restricted,
            'written_by' => $actor !== null ? $actor->id : auth()->id(),
        ]);

        $this->auditor->record(
            AuditAction::IncidentNoteAdded,
            $note,
            ['incident_id' => $incident->id, 'restricted' => $restricted],
            $actor,
        );

        return $note;
    }
}
