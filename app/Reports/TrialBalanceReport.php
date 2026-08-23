<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Models\LedgerAccount;
use App\Traits\ReadsFinanceWindow;
use Illuminate\Support\Collection;

/**
 * Every account with what was debited and credited to it.
 *
 * The two totals at the bottom must match. When they do not, something has
 * gone wrong that no other report will show.
 */
class TrialBalanceReport implements Report
{
    use ReadsFinanceWindow;

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'trial-balance';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Trial balance';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Code', 'Account', 'Kind', 'Debit', 'Credit', 'Balance'];
    }

    /**
     * Build the rows of the report.
     *
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $schoolId = $this->schoolId($parameters);
        $window = $this->window($parameters);

        $accounts = LedgerAccount::query()
            ->where('school_id', $schoolId)
            ->orderBy('code')
            ->get();

        $rows = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($accounts as $account) {
            $lines = $this->linesIn($schoolId, $window)->where('ledger_account_id', $account->id);
            $debit = round((float) (clone $lines)->sum('debit'), 2);
            $credit = round((float) (clone $lines)->sum('credit'), 2);

            if ($debit === 0.0 && $credit === 0.0) {
                continue;
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $rows[] = [
                $account->code,
                $account->name,
                $account->type->label(),
                $debit,
                $credit,
                $this->balanceOfAccount($schoolId, $window, $account),
            ];
        }

        $rows[] = ['', 'Total', '', round($totalDebit, 2), round($totalCredit, 2), ''];

        return $this->asRows($rows);
    }
}
