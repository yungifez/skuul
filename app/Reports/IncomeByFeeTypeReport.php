<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Models\Fee;
use App\Traits\ReadsFinanceWindow;
use Brick\Money\Money as BrickMoney;
use Illuminate\Support\Collection;

/**
 * What each fee raised, waived, collected, and is still owed.
 *
 * The ledger says what the school earned in total. This says which fee it
 * came from, which is the question a school board actually asks.
 */
class IncomeByFeeTypeReport implements Report
{
    use ReadsFinanceWindow;

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'income-by-fee-type';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Income by fee type';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Category', 'Fee', 'Invoices', 'Charged', 'Waived', 'Fines', 'Collected', 'Still owed'];
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
        $currency = config('app.currency');

        $fees = Fee::query()
            ->whereRelation('feeCategory', 'school_id', $schoolId)
            ->with('feeCategory')
            ->orderBy('fee_category_id')
            ->orderBy('name')
            ->get();

        $rows = [];

        foreach ($fees as $fee) {
            $lines = $fee->feeInvoiceRecords()
                ->whereHas('feeInvoice', function ($invoice) use ($from, $to): void {
                    if ($from !== null) {
                        $invoice->where('issue_date', '>=', $from);
                    }

                    if ($to !== null) {
                        $invoice->where('issue_date', '<=', $to);
                    }
                })
                ->with('allocations')
                ->get();

            if ($lines->isEmpty()) {
                continue;
            }

            $charged = $lines->sum(fn ($line): int => $line->amount->getMinorAmount()->toInt());
            $waived = $lines->sum(fn ($line): int => $line->waiver->getMinorAmount()->toInt());
            $fines = $lines->sum(fn ($line): int => $line->fine->getMinorAmount()->toInt());
            $paid = $lines->sum(fn ($line): int => $line->paid->getMinorAmount()->toInt());

            $rows[] = [
                $fee->feeCategory?->name,
                $fee->name,
                $lines->count(),
                BrickMoney::ofMinor($charged, $currency)->getAmount()->toFloat(),
                BrickMoney::ofMinor($waived, $currency)->getAmount()->toFloat(),
                BrickMoney::ofMinor($fines, $currency)->getAmount()->toFloat(),
                BrickMoney::ofMinor($paid, $currency)->getAmount()->toFloat(),
                BrickMoney::ofMinor(max($charged + $fines - $waived - $paid, 0), $currency)->getAmount()->toFloat(),
            ];
        }

        return $this->asRows($rows);
    }
}
