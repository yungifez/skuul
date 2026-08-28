<?php

namespace App\Traits;

use App\Enums\LedgerAccountType;
use App\Models\AcademicYear;
use App\Models\FinancialPeriod;
use App\Models\LedgerAccount;
use App\Models\LedgerLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The stretch of time a finance report covers.
 *
 * A report that says nothing about its dates is not worth printing, so every
 * finance report answers for one window and says which one it used.
 */
trait ReadsFinanceWindow
{
    /**
     * Work out the first and last day the report covers.
     *
     * The caller may name the days. When it does not, the running cycle
     * decides, because that is the stretch a school office thinks in.
     *
     * @param  array<string, mixed>  $parameters
     * @return array{0: string|null, 1: string|null}
     */
    protected function window(array $parameters): array
    {
        $from = $parameters['from'] ?? null;
        $to = $parameters['to'] ?? null;

        if ($from !== null || $to !== null) {
            return [is_string($from) ? $from : null, is_string($to) ? $to : null];
        }

        if (isset($parameters['financial_period_id'])) {
            $period = FinancialPeriod::query()
                ->where('school_id', $this->schoolId($parameters))
                ->find($parameters['financial_period_id']);

            if ($period !== null) {
                return [$period->starts_on->toDateString(), $period->ends_on->toDateString()];
            }
        }

        $cycle = isset($parameters['academic_year_id'])
            ? AcademicYear::find($parameters['academic_year_id'])
            : AcademicYear::find(current_academic_year_id());

        return [$cycle?->starts_on?->toDateString(), $cycle?->ends_on?->toDateString()];
    }

    /**
     * Limit a line query to one school and one window.
     *
     * @param  array{0: string|null, 1: string|null}  $window
     * @return Builder<LedgerLine>
     */
    protected function linesIn(int $schoolId, array $window): Builder
    {
        [$from, $to] = $window;

        return LedgerLine::query()->whereHas('transaction', function (Builder $transaction) use ($schoolId, $from, $to): void {
            $transaction->where('school_id', $schoolId);

            if ($from !== null) {
                $transaction->where('transaction_date', '>=', $from);
            }

            if ($to !== null) {
                $transaction->where('transaction_date', '<=', $to);
            }
        });
    }

    /**
     * Total one kind of account, and list the accounts behind the total.
     *
     * An account nothing was written to is left out, so a school reads its
     * own figures rather than a page of zeros.
     *
     * @param  array{0: string|null, 1: string|null}  $window
     * @return array{0: array<int, array<int, mixed>>, 1: float}
     */
    protected function totalByKind(int $schoolId, array $window, LedgerAccountType $type, string $heading): array
    {
        $accounts = LedgerAccount::query()
            ->where('school_id', $schoolId)
            ->where('type', $type)
            ->orderBy('code')
            ->get();

        $rows = [];
        $total = 0.0;

        foreach ($accounts as $account) {
            $amount = $this->balanceOfAccount($schoolId, $window, $account);

            if ($amount === 0.0) {
                continue;
            }

            $total += $amount;
            $rows[] = [$heading, $account->name, $amount];
        }

        return [$rows, round($total, 2)];
    }

    /**
     * Get what one account holds over the window, on its own normal side.
     *
     * @param  array{0: string|null, 1: string|null}  $window
     */
    protected function balanceOfAccount(int $schoolId, array $window, LedgerAccount $account): float
    {
        $lines = $this->linesIn($schoolId, $window)->where('ledger_account_id', $account->id);
        $debit = (float) (clone $lines)->sum('debit');
        $credit = (float) (clone $lines)->sum('credit');

        return round($account->type->normalBalance() === 'debit' ? $debit - $credit : $credit - $debit, 2);
    }

    /**
     * Turn the rows a report built into the collection it must return.
     *
     * @param  array<int, array<int, mixed>>  $rows
     * @return Collection<int, array<int, mixed>>
     */
    protected function asRows(array $rows): Collection
    {
        return collect($rows);
    }

    /**
     * Get the school the report is about.
     *
     * @param  array<string, mixed>  $parameters
     */
    protected function schoolId(array $parameters): int
    {
        return (int) ($parameters['school_id'] ?? current_school_id());
    }
}
