<?php

namespace App\Services\Fee;

use App\Exceptions\InvalidValueException;
use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use Brick\Money\Money;

class FeeInvoiceRecordService
{
    /**
     * Store a new fee invoice record.
     *
     * @param  array  $records
     */
    public function storeFeeInvoiceRecord($records): FeeInvoiceRecord
    {
        $fee = Fee::where('id', $records['fee_id'])->whereRelation('feeCategory', 'school_id', current_school_id())->get();
        $feeInvoice = FeeInvoice::where('id', $records['fee_invoice_id'])->ofSchool()->get();

        if ($fee->isEmpty() || $feeInvoice->isEmpty()) {
            throw new InvalidValueException("The fee you selected doesn't exist");
        }

        if ($feeInvoice->first()->ledgerTransaction !== null) {
            throw new InvalidValueException('A posted invoice cannot have lines added to it. Create a correcting invoice instead.');
        }

        $feeInvoiceRecord = FeeInvoiceRecord::create([
            'fee_invoice_id' => $records['fee_invoice_id'],
            'fee_id' => $records['fee_id'],
            'amount' => $records['amount'],
            'waiver' => $records['waiver'] ?? 0,
            'fine' => $records['fine'] ?? 0,
        ]);

        return $feeInvoiceRecord;
    }

    /**
     * Update a fee invoice record.
     */
    public function updateFeeInvoiceRecord(FeeInvoiceRecord $feeInvoiceRecord, $records): FeeInvoiceRecord
    {
        if ($feeInvoiceRecord->feeInvoice?->ledgerTransaction !== null) {
            throw new InvalidValueException('A posted invoice cannot have its lines changed. Create a correcting invoice instead.');
        }

        // The form asks for whole units, and the column keeps minor units,
        // which is what the Money cast does on the way in.
        $amount = Money::of($records['amount'], config('app.currency'));
        $waiver = Money::of($records['waiver'] ?? 0, config('app.currency'));
        $fine = Money::of($records['fine'] ?? 0, config('app.currency'));

        if ($this->isPaymentHigherThanDue($amount, $feeInvoiceRecord->paid, $waiver, $fine)) {
            throw new InvalidValueException('Due Cannot be less than amount already paid');
        }

        $feeInvoiceRecord->update([
            'amount' => $records['amount'],
            'waiver' => $records['waiver'] ?? 0,
            'fine' => $records['fine'] ?? 0,
        ]);

        return $feeInvoiceRecord;
    }

    /**
     * Delete a fee invoice.
     */
    public function deleteFeeInvoiceRecord(FeeInvoiceRecord $feeInvoiceRecord): void
    {
        if ($feeInvoiceRecord->feeInvoice?->ledgerTransaction !== null) {
            throw new InvalidValueException('A posted invoice cannot have its lines removed. Create a correcting invoice instead.');
        }

        $feeInvoiceRecord->delete();
    }

    /**
     * Check whether a line would be left owing less than it has been paid.
     */
    public function isPaymentHigherThanDue(Money $amount, Money $paid, Money $waiver, Money $fine): bool
    {
        $due = $amount->plus($fine)->minus($waiver);

        if ($due->isLessThan($paid)) {
            return true;
        }

        return false;
    }
}
