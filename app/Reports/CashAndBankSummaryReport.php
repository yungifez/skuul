<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Enums\LedgerAccountType;
use App\Models\LedgerAccount;
use App\Traits\ReadsFinanceWindow;
use Illuminate\Support\Collection;

/**
 * What the school held, what came in, what went out, and what is left.
 *
 * This is the report somebody reads before counting the cash box, so it shows
 * the opening figure as well as the closing one.
 */
class CashAndBankSummaryReport implements Report
{
    use ReadsFinanceWindow;

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'cash-and-bank';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Cash and bank summary';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Account', 'Opening', 'Money in', 'Money out', 'Closing'];
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
        [$from, $to] = $this->window($parameters);

        $accounts = LedgerAccount::query()
            ->where('school_id', $schoolId)
            ->where('type', LedgerAccountType::Asset)
            ->whereIn('purpose', ['cash', 'bank'])
            ->orderBy('code')
            ->get();

        $rows = [];

        foreach ($accounts as $account) {
            $inWindow = $this->linesIn($schoolId, [$from, $to])->where('ledger_account_id', $account->id);
            $in = round((float) (clone $inWindow)->sum('debit'), 2);
            $out = round((float) (clone $inWindow)->sum('credit'), 2);

            // Everything before the window opened is the opening figure.
            $opening = $from === null
                ? 0.0
                : $this->balanceOfAccount($schoolId, [null, date('Y-m-d', strtotime("$from -1 day"))], $account);

            $rows[] = [
                $account->name,
                $opening,
                $in,
                $out,
                round($opening + $in - $out, 2),
            ];
        }

        return $this->asRows($rows);
    }
}
