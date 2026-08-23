<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\Budget;
use App\Models\LedgerAccount;
use App\Models\Program;
use App\Models\User;

/**
 * Say what one account is allowed over one stretch of the year.
 *
 * A budget is a plan, so setting it again revises the same plan rather than
 * making a second one. Every revision is written to the audit log, because a
 * budget that quietly grows is how overspending hides.
 */
class SetBudget
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Write or revise the plan.
     *
     * @throws InvalidValueException when the plan does not belong together
     */
    public function set(
        AcademicYear $academicYear,
        LedgerAccount $account,
        float $amount,
        ?AcademicPeriod $academicPeriod = null,
        ?Program $program = null,
        ?string $fund = null,
        ?string $note = null,
        ?User $actor = null,
    ): Budget {
        if ($amount < 0) {
            throw new InvalidValueException('A budget cannot be less than nothing.');
        }

        if ($account->school_id !== $academicYear->school_id) {
            throw new InvalidValueException('That account belongs to another campus.');
        }

        if ($academicPeriod !== null && $academicPeriod->academic_year_id !== $academicYear->id) {
            throw new InvalidValueException('That term is not part of this cycle.');
        }

        if ($program !== null && $program->school_id !== $academicYear->school_id) {
            throw new InvalidValueException('That programme belongs to another campus.');
        }

        $fund = $fund === null || trim($fund) === '' ? null : trim($fund);

        $hash = Budget::hashFor(
            $academicYear->id,
            $academicPeriod?->id,
            $account->id,
            $program?->id,
            $fund,
        );

        $budget = Budget::firstOrNew([
            'school_id' => $academicYear->school_id,
            'scope_hash' => $hash,
        ]);

        $was = $budget->exists ? $budget->amount : null;

        $budget->fill([
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod?->id,
            'ledger_account_id' => $account->id,
            'program_id' => $program?->id,
            'fund' => $fund,
            'amount' => round($amount, 2),
            'note' => $note,
            'set_by' => $actor === null ? auth()->id() : $actor->id,
        ])->save();

        $this->auditor->record(
            AuditAction::BudgetSet,
            $budget,
            [
                'account' => $account->name,
                'was' => $was,
                'now' => $budget->amount,
                'covers' => $budget->coverage(),
            ],
            $actor,
            $academicYear->school_id,
        );

        return $budget;
    }
}
