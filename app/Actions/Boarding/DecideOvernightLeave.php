<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\OvernightLeaveStatus;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingPlace;
use App\Models\OvernightLeave;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($leave, $status, $note, $actor): OvernightLeave {
            // Two people may answer one request at once. Read it again under
            // a lock, so the second answer meets the first instead of hiding it.
            $leave = OvernightLeave::query()->lockForUpdate()->findOrFail($leave->id);

            if (!$leave->status->canMoveTo($status)) {
                throw new InvalidValueException("This request was answered already: {$leave->status->label()}.");
            }

            if ($status === OvernightLeaveStatus::Approved && $leave->returns_on->isBefore(today())) {
                throw new InvalidValueException('The nights on this request have passed. Refuse it instead.');
            }

            if ($status === OvernightLeaveStatus::Approved && !$this->stillBoardsAt($leave)) {
                throw new InvalidValueException('This learner no longer boards in this house. Refuse the request instead.');
            }

            if ($status === OvernightLeaveStatus::Returned && $leave->leaves_on->isAfter(today())) {
                throw new InvalidValueException('This learner has not left yet. Cancel the night away instead.');
            }

            if ($status === OvernightLeaveStatus::Cancelled && $leave->status === OvernightLeaveStatus::Approved && !$leave->leaves_on->isAfter(today())) {
                throw new InvalidValueException('This learner has left already. Record them back in the house instead.');
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
        });
    }

    /**
     * Check if the learner still has a bed at the campus the request was made to.
     */
    private function stillBoardsAt(OvernightLeave $leave): bool
    {
        $place = BoardingPlace::query()
            ->where('student_record_id', $leave->student_record_id)
            ->orderByDesc('id')
            ->first();

        return $place !== null && $place->isBoarding() && $place->school_id === $leave->school_id;
    }
}
