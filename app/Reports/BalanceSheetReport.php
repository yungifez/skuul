<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Enums\LedgerAccountType;
use App\Traits\ReadsFinanceWindow;
use Illuminate\Support\Collection;

/**
 * What the school owns, what it owes, and what is left over.
 *
 * The surplus earned so far is shown beside equity, so the two sides of the
 * sheet meet without anybody closing the books by hand.
 */
class BalanceSheetReport implements Report
{
    use ReadsFinanceWindow;

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'balance-sheet';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Balance sheet';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Section', 'Account', 'Amount'];
    }

    /**
     * Build the rows of the report.
     *
     * The sheet says where the school stood on the last day of the window, so
     * every line up to that day counts, not only the ones inside it.
     *
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $schoolId = $this->schoolId($parameters);
        [, $to] = $this->window($parameters);
        $window = [null, $to];

        [$assetRows, $assets] = $this->totalByKind($schoolId, $window, LedgerAccountType::Asset, 'Assets');
        [$liabilityRows, $liabilities] = $this->totalByKind($schoolId, $window, LedgerAccountType::Liability, 'Liabilities');
        [$equityRows, $equity] = $this->totalByKind($schoolId, $window, LedgerAccountType::Equity, 'Equity');
        [, $income] = $this->totalByKind($schoolId, $window, LedgerAccountType::Income, 'Income');
        [, $expenses] = $this->totalByKind($schoolId, $window, LedgerAccountType::Expense, 'Expenses');

        $surplus = round($income - $expenses, 2);

        $rows = array_merge(
            $assetRows,
            [['Assets', 'Total assets', round($assets, 2)]],
            $liabilityRows,
            [['Liabilities', 'Total liabilities', round($liabilities, 2)]],
            $equityRows,
            [['Equity', 'Surplus so far', $surplus]],
            [['Equity', 'Total equity', round($equity + $surplus, 2)]],
            [['Check', 'Liabilities and equity', round($liabilities + $equity + $surplus, 2)]],
        );

        return $this->asRows($rows);
    }
}
