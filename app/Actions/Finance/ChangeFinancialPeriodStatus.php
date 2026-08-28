<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\FinancialPeriodStatus;
use App\Exceptions\InvalidValueException;
use App\Models\FinancialPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChangeFinancialPeriodStatus
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Close or reopen a school's finance period.
     *
     * Closing affects new finance postings only. It does not change academic
     * periods or alter invoices and payments already in the books.
     */
    public function change(FinancialPeriod $period, FinancialPeriodStatus $status, ?string $reason = null, ?User $actor = null): FinancialPeriod
    {
        if ($period->school_id !== current_school_id()) {
            throw new InvalidValueException('That financial period belongs to another school.');
        }

        if ($period->status === $status) {
            throw new InvalidValueException("This financial period is already {$status->label()}.");
        }

        if ($status === FinancialPeriodStatus::Open && trim((string) $reason) === '') {
            throw new InvalidValueException('Say why this financial period is being reopened.');
        }

        return DB::transaction(function () use ($period, $status, $reason, $actor): FinancialPeriod {
            $period->forceFill([
                'status' => $status,
                'closed_at' => $status === FinancialPeriodStatus::Closed ? now() : null,
                'closed_by' => $status === FinancialPeriodStatus::Closed ? ($actor?->id ?? auth()->id()) : null,
                'close_reason' => $reason,
            ])->saveQuietly();

            $this->auditor->record(
                $status === FinancialPeriodStatus::Closed ? AuditAction::FinancialPeriodClosed : AuditAction::FinancialPeriodReopened,
                $period,
                ['reason' => $reason],
                $actor,
                $period->school_id,
            );

            return $period;
        });
    }
}
