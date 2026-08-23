<?php

namespace App\Services\Finance;

use App\Exceptions\InvalidValueException;
use App\Models\FeeInvoiceRecord;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Decide which invoice lines a payment settles.
 *
 * A family that hands over part of what it owes expects the oldest bill to
 * clear first. An office that names the lines itself expects the application
 * to obey, and to refuse anything that would overpay a line.
 */
class AllocationPlanner
{
    /**
     * Get the lines a student still owes money on, oldest bill first.
     *
     * @return Collection<int, FeeInvoiceRecord>
     */
    public function openLinesFor(StudentRecord $enrollment, ?int $onlyInvoice = null): Collection
    {
        return FeeInvoiceRecord::query()
            ->isDue()
            ->whereHas('feeInvoice', function (Builder $invoice) use ($enrollment, $onlyInvoice): void {
                $invoice->where('user_id', $enrollment->user_id);

                if ($onlyInvoice !== null) {
                    $invoice->whereKey($onlyInvoice);
                }
            })
            ->with(['feeInvoice', 'fee'])
            ->join('fee_invoices', 'fee_invoices.id', '=', 'fee_invoice_records.fee_invoice_id')
            ->orderBy('fee_invoices.due_date')
            ->orderBy('fee_invoices.id')
            ->orderBy('fee_invoice_records.id')
            ->select('fee_invoice_records.*')
            ->get();
    }

    /**
     * Spread an amount across the oldest open lines.
     *
     * Whatever is left over is not forced onto a line. It stays with the
     * payment as credit the next invoice can use.
     *
     * @return array<int, int> the minor amount to write against each line id
     */
    public function spread(StudentRecord $enrollment, int $amount, ?int $onlyInvoice = null): array
    {
        $left = $amount;
        $plan = [];

        foreach ($this->openLinesFor($enrollment, $onlyInvoice) as $line) {
            if ($left <= 0) {
                break;
            }

            $owed = $line->outstanding->getMinorAmount()->toInt();

            if ($owed <= 0) {
                continue;
            }

            $take = min($owed, $left);
            $plan[$line->id] = $take;
            $left -= $take;
        }

        return $plan;
    }

    /**
     * Check a plan the office wrote itself.
     *
     * @param  array<int|string, int|string>  $requested  the minor amount for each line id
     * @return array<int, int>
     *
     * @throws InvalidValueException when a line is not this student's, is
     *                               overpaid, or the plan is worth more than
     *                               the payment
     */
    public function check(StudentRecord $enrollment, int $amount, array $requested): array
    {
        $plan = [];

        foreach ($requested as $lineId => $share) {
            $share = (int) $share;

            if ($share === 0) {
                continue;
            }

            if ($share < 0) {
                throw new InvalidValueException('A payment cannot take money off an invoice line.');
            }

            $plan[(int) $lineId] = $share;
        }

        if ($plan === []) {
            return [];
        }

        $lines = FeeInvoiceRecord::query()
            ->whereKey(array_keys($plan))
            ->whereHas('feeInvoice', fn (Builder $invoice) => $invoice->where('user_id', $enrollment->user_id))
            ->get()
            ->keyBy('id');

        foreach ($plan as $lineId => $share) {
            $line = $lines->get($lineId);

            if ($line === null) {
                throw new InvalidValueException('One of these fees is not on this student\'s invoices.');
            }

            if ($share > $line->outstanding->getMinorAmount()->toInt()) {
                throw new InvalidValueException('One of these fees was given more than it still owes.');
            }
        }

        if (array_sum($plan) > $amount) {
            throw new InvalidValueException('The fees named add up to more than the payment.');
        }

        return $plan;
    }
}
