<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Models\LedgerLine;
use App\Traits\ReadsFinanceWindow;
use Illuminate\Support\Collection;

/**
 * What the school spent, listed line by line.
 *
 * Each line keeps its fund and its programme, so a school group can see who
 * spent the money as well as what it went on.
 */
class ExpenseReport implements Report
{
    use ReadsFinanceWindow;

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'expenses';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Expenses';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Date', 'Account', 'Description', 'Programme', 'Fund', 'Amount'];
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
            ->whereRelation('account', 'type', 'expense')
            ->when(
                isset($parameters['fund']),
                fn ($query) => $query->where('fund', $parameters['fund']),
            )
            ->when(
                isset($parameters['program_id']),
                fn ($query) => $query->where('program_id', $parameters['program_id']),
            )
            ->with(['transaction', 'account', 'program'])
            ->orderBy('id')
            ->get();

        /** @var Collection<int, array<int, mixed>> $rows */
        $rows = $lines->map(fn (LedgerLine $line): array => [
            $line->transaction?->transaction_date?->toDateString(),
            $line->account?->name,
            $line->memo ?? $line->transaction?->description,
            $line->program?->name,
            $line->fund,
            round($line->debit - $line->credit, 2),
        ])->values()->toBase();

        return $rows;
    }
}
