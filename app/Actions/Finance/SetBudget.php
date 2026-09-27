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
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

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
        $note = $note === null || trim($note) === '' ? null : trim($note);

        try {
            return $this->write($academicYear, $account, round($amount, 2), $academicPeriod, $program, $fund, $note, $actor);
        } catch (UniqueConstraintViolationException) {
            // Somebody wrote the same plan a moment ago, so revise theirs.
            return $this->write($academicYear, $account, round($amount, 2), $academicPeriod, $program, $fund, $note, $actor);
        }
    }

    /**
     * Drop a plan, and say so in the audit log.
     */
    public function remove(Budget $budget, ?User $actor = null): void
    {
        DB::transaction(function () use ($budget, $actor): void {
            $budget->loadMissing('account:id,name');

            $this->auditor->record(
                AuditAction::BudgetRemoved,
                $budget,
                ['account' => $budget->account?->name, 'was' => $budget->amount, 'covers' => $budget->coverage()],
                $actor,
                $budget->school_id,
            );

            $budget->delete();
        });
    }

    /**
     * Write the plan, revising the one that covers the same ground.
     */
    private function write(
        AcademicYear $academicYear,
        LedgerAccount $account,
        float $amount,
        ?AcademicPeriod $academicPeriod,
        ?Program $program,
        ?string $fund,
        ?string $note,
        ?User $actor,
    ): Budget {
        return DB::transaction(function () use ($academicYear, $account, $amount, $academicPeriod, $program, $fund, $note, $actor): Budget {
            // The books match a fund without regard to capitals, so the plan
            // must too. Otherwise "Library" and "library" are two plans that
            // both count the same spending.
            $budget = Budget::query()
                ->where('school_id', $academicYear->school_id)
                ->where('academic_year_id', $academicYear->id)
                ->where('academic_period_id', $academicPeriod?->id)
                ->where('ledger_account_id', $account->id)
                ->where('program_id', $program?->id)
                ->when(
                    $fund === null,
                    fn ($query) => $query->whereNull('fund'),
                    fn ($query) => $query->whereRaw('LOWER(fund) = ?', [mb_strtolower((string) $fund)]),
                )
                ->lockForUpdate()
                ->first()
                ?? new Budget([
                    'school_id' => $academicYear->school_id,
                    'scope_hash' => Budget::hashFor($academicYear->id, $academicPeriod?->id, $account->id, $program?->id, $fund),
                    'fund' => $fund,
                ]);

            $was = $budget->exists ? $budget->amount : null;

            if ($was === $amount && $budget->note === $note) {
                return $budget;
            }

            $budget->fill([
                'academic_year_id' => $academicYear->id,
                'academic_period_id' => $academicPeriod?->id,
                'ledger_account_id' => $account->id,
                'program_id' => $program?->id,
                'amount' => $amount,
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
        });
    }
}
