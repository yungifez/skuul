<?php

namespace App\Services\Finance;

use App\Models\AcademicYear;
use App\Models\Budget;
use App\Models\LedgerLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Put each plan beside what actually happened.
 *
 * The plan comes from the budgets a school wrote. What happened comes from the
 * books, which nobody can edit, so the comparison is worth reading.
 */
class BudgetVersusActual
{
    /**
     * Compare every plan of one cycle with the books.
     *
     * @return Collection<int, BudgetComparison>
     */
    public function forCycle(AcademicYear $academicYear): Collection
    {
        $budgets = Budget::query()
            ->forCycle($academicYear->id)
            ->inSchool($academicYear->school_id)
            ->with(['account', 'academicPeriod', 'program'])
            ->get();

        $rows = [];

        foreach ($budgets as $budget) {
            $rows[] = $this->compare($budget);
        }

        usort($rows, fn (BudgetComparison $first, BudgetComparison $second): int => $second->difference() <=> $first->difference());

        return collect($rows);
    }

    /**
     * Compare one plan with the books.
     */
    public function compare(Budget $budget): BudgetComparison
    {
        return new BudgetComparison($budget, round($budget->amount, 2), $this->actualFor($budget));
    }

    /**
     * Get what the books say happened on one plan's account and dimensions.
     */
    public function actualFor(Budget $budget): float
    {
        $account = $budget->account;

        if ($account === null) {
            return 0.0;
        }

        [$from, $to] = $this->window($budget);

        $lines = LedgerLine::query()
            ->where('ledger_account_id', $account->id)
            ->when($budget->program_id !== null, fn (Builder $query) => $query->where('program_id', $budget->program_id))
            ->when($budget->fund !== null, fn (Builder $query) => $query->where('fund', $budget->fund))
            ->whereHas('transaction', function (Builder $transaction) use ($from, $to): void {
                if ($from !== null) {
                    $transaction->where('transaction_date', '>=', $from);
                }

                if ($to !== null) {
                    $transaction->where('transaction_date', '<=', $to);
                }
            });

        $debit = (float) (clone $lines)->sum('debit');
        $credit = (float) (clone $lines)->sum('credit');

        return round($account->type->normalBalance() === 'debit' ? $debit - $credit : $credit - $debit, 2);
    }

    /**
     * Get the stretch of the year the plan covers.
     *
     * @return array{0: string|null, 1: string|null}
     */
    private function window(Budget $budget): array
    {
        $period = $budget->academicPeriod;

        if ($period !== null) {
            return [$period->starts_on?->toDateString(), $period->ends_on?->toDateString()];
        }

        $year = $budget->academicYear;

        return [$year?->starts_on?->toDateString(), $year?->ends_on?->toDateString()];
    }
}
