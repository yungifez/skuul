<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Models\LedgerLine;
use App\Traits\ReadsFinanceWindow;
use Illuminate\Support\Collection;

/**
 * Every line the school posted, in the order it was posted.
 *
 * This is the report an auditor asks for. Nothing is summarised, because the
 * point is to see each entry as it was written.
 */
class GeneralLedgerReport implements Report
{
    use ReadsFinanceWindow;

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'general-ledger';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'General ledger';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Date', 'Entry', 'Description', 'Account', 'Memo', 'Student', 'Fund', 'Debit', 'Credit'];
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

        $lines = $this->linesIn($schoolId, $this->window($parameters))
            ->when(
                isset($parameters['ledger_account_id']),
                fn ($query) => $query->where('ledger_account_id', $parameters['ledger_account_id']),
            )
            ->with(['transaction', 'account', 'studentRecord.user'])
            ->orderBy('ledger_transaction_id')
            ->orderBy('id')
            ->get();

        /** @var Collection<int, array<int, mixed>> $rows */
        $rows = $lines->map(fn (LedgerLine $line): array => [
            $line->transaction?->transaction_date?->toDateString(),
            $line->ledger_transaction_id,
            $line->transaction?->description,
            $line->account?->name,
            $line->memo,
            $line->studentRecord?->user?->name,
            $line->fund,
            $line->debit,
            $line->credit,
        ])->values()->toBase();

        return $rows;
    }
}
