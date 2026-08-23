<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Enums\LedgerAccountType;
use App\Traits\ReadsFinanceWindow;
use Illuminate\Support\Collection;

/**
 * What the school earned and what it spent, and the difference.
 *
 * A school board asks one question first: did we take in more than we paid
 * out? This report answers it and shows the accounts behind the answer.
 */
class IncomeStatementReport implements Report
{
    use ReadsFinanceWindow;

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'income-statement';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Income statement';
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
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $schoolId = $this->schoolId($parameters);
        $window = $this->window($parameters);

        [$incomeRows, $income] = $this->totalByKind($schoolId, $window, LedgerAccountType::Income, 'Income');
        [$expenseRows, $expenses] = $this->totalByKind($schoolId, $window, LedgerAccountType::Expense, 'Expenses');

        $rows = array_merge(
            $incomeRows,
            [['Income', 'Total income', round($income, 2)]],
            $expenseRows,
            [['Expenses', 'Total expenses', round($expenses, 2)]],
            [['Result', $income >= $expenses ? 'Surplus' : 'Deficit', round($income - $expenses, 2)]],
        );

        return $this->asRows($rows);
    }
}
