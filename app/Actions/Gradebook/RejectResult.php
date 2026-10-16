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
 * Reject one submitted result and keep it out of official records.
 */
class RejectResult
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * @throws InvalidValueException
     */
    public function reject(ResultSnapshot $result, User $actor, string $reason): ResultSnapshot
    {
        if ($result->school_id !== current_school_id()) {
            throw new InvalidValueException('The result belongs to another school.');
        }

        if ($result->approval_status !== ResultApprovalStatus::Pending) {
            throw new InvalidValueException('Only a result awaiting approval can be rejected.');
        }

        return DB::transaction(function () use ($result, $actor, $reason): ResultSnapshot {
            $result = ResultSnapshot::query()->lockForUpdate()->findOrFail($result->id);

            if ($result->approval_status !== ResultApprovalStatus::Pending) {
                throw new InvalidValueException('Only a result awaiting approval can be rejected.');
            }

            $result->reject($actor, $reason);

            $this->auditor->record(
                AuditAction::ResultRejected,
                $result,
                ['revision' => $result->revision, 'reason' => $reason],
                $actor,
            );

            return $result->refresh();
        });
    }
}
