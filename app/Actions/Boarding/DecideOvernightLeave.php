<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\OvernightLeaveStatus;
use App\Exceptions\InvalidValueException;
use App\Models\OvernightLeave;
use App\Models\User;

/**
 * Answer a request for a night away, and record the learner coming back.
 *
 * The states only move forward. A refused request is never quietly approved
 * later, because the family and the house both acted on the answer.
 */
class DecideOvernightLeave
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Move the request to its next state.
     *
     * @throws InvalidValueException when the move is not allowed
     */
    public function decide(
        OvernightLeave $leave,
        OvernightLeaveStatus $status,
        ?string $note = null,
        ?User $actor = null,
    ): OvernightLeave {
        if (!$leave->status->canMoveTo($status)) {
            throw new InvalidValueException(
                "A request that is {$leave->status->label()} cannot become {$status->label()}.",
            );
        }

        $was = $leave->status;
        $leave->status = $status;

        if ($status === OvernightLeaveStatus::Returned) {
            $leave->returned_at = now();
        } else {
            $leave->decided_by = $actor === null ? auth()->id() : $actor->id;
            $leave->decided_at = now();
            $leave->decision_note = $note;
        }

        $leave->save();

        $this->auditor->record(
            $status === OvernightLeaveStatus::Returned
                ? AuditAction::OvernightLeaveReturned
                : AuditAction::OvernightLeaveDecided,
            $leave,
            ['was' => $was->value, 'now' => $status->value, 'note' => $note],
            $actor,
            $leave->school_id,
        );

        return $leave;
    }
}
