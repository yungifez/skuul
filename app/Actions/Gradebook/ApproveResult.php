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

        return DB::transaction(function () use ($result, $actor, $reason): ResultSnapshot {
            $result = ResultSnapshot::query()->lockForUpdate()->findOrFail($result->id);

            if ($result->approval_status !== ResultApprovalStatus::Pending) {
                throw new InvalidValueException('Only a result awaiting approval can be approved.');
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
