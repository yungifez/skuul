<?php

namespace App\Actions\Gradebook;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\ResultApprovalStatus;
use App\Exceptions\InvalidValueException;
use App\Models\ResultSnapshot;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Approve one submitted result so it becomes visible to official readers.
 *
 * The person who sent a result never approves it, so each official result
 * has passed two pairs of hands.
 */
class ApproveResult
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * @throws InvalidValueException
     */
    public function approve(ResultSnapshot $result, User $actor, ?string $reason = null): ResultSnapshot
    {
        if ($result->school_id !== current_school_id()) {
            throw new InvalidValueException('The result belongs to another school.');
        }

        if ($result->approval_status !== ResultApprovalStatus::Pending) {
            throw new InvalidValueException('Only a result awaiting approval can be approved.');
        }

        // A second person checks every result. The one who sent it cannot.
        if ($result->published_by === $actor->id) {
            throw new InvalidValueException('You sent this result, so somebody else approves it.');
        }

        return DB::transaction(function () use ($result, $actor, $reason): ResultSnapshot {
            $result = ResultSnapshot::query()->lockForUpdate()->findOrFail($result->id);

            if ($result->approval_status !== ResultApprovalStatus::Pending) {
                throw new InvalidValueException('Only a result awaiting approval can be approved.');
            }

            $newerRevision = ResultSnapshot::query()
                ->where('student_record_id', $result->student_record_id)
                ->where('course_offering_id', $result->course_offering_id)
                ->where('revision', '>', $result->revision)
                ->max('revision');

            if ($newerRevision !== null) {
                throw new InvalidValueException("The teacher sent revision $newerRevision after this one. Approve that one instead.");
            }

            $result->approve($actor, $reason);

            $this->auditor->record(
                AuditAction::ResultApproved,
                $result,
                ['revision' => $result->revision, 'reason' => $reason],
                $actor,
            );

            return $result->refresh();
        });
    }
}
