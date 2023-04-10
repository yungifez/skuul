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
     * @param array $records
     */
    public function storeFeeInvoiceRecord($records): FeeInvoiceRecord
    {
        $fee = Fee::where('id', $records['fee_id'])->whereRelation('feeCategory', 'school_id', auth()->user()->school_id)->get();
        $feeInvoice = FeeInvoice::where('id', $records['fee_invoice_id'])->whereRelation('user', 'school_id', auth()->user()->school_id)->get();

        if ($fee->isEmpty() || $feeInvoice->isEmpty()) {
            throw new InvalidValueException("The fee you selected doesn't exist");
        }

        $feeInvoiceRecord = FeeInvoiceRecord::create([
            'fee_invoice_id' => $records['fee_invoice_id'],
            'fee_id'         => $records['fee_id'],
            'amount'         => $records['amount'],
            'waiver'         => $records['waiver'] ?? 0,
            'fine'           => $records['fine'] ?? 0,
        ]);

        return $feeInvoiceRecord;
    }

    /**
     * Update a fee invoice record.
     *
     * @param FeeInvoiceRecord $feeInvoiceRecord
     * @param                  $records
     */
    public function updateFeeInvoiceRecord(FeeInvoiceRecord $feeInvoiceRecord, $records): FeeInvoiceRecord
    {
        $amount = Money::ofMinor($records['amount'], config('app.currency'));
        $waiver = Money::ofMinor($records['waiver'] ?? 0, config('app.currency'));
        $fine = Money::ofMinor($records['fine'] ?? 0, config('app.currency'));

        if ($this->isPaymentHigherThanDue($amount, $feeInvoiceRecord->paid, $waiver, $fine)) {
            throw new InvalidValueException('Due Cannot be less than amount already paid');
        }

        $feeInvoiceRecord->update([
            'amount' => $records['amount'],
            'waiver' => $records['waiver'] ?? 0,
            'fine'   => $records['fine'] ?? 0,
        ]);

        return $feeInvoiceRecord;
    }

    /**
     * Delete a fee invoice.
     *
     * @param FeeInvoiceRecord $feeInvoiceRecord
     */
    public function deleteFeeInvoiceRecord(FeeInvoiceRecord $feeInvoiceRecord): void
    {
        $feeInvoiceRecord->delete();
    }

    /**
     * Add a new paymeny.
     *
     * @param FeeInvoiceRecord $feeInvoiceRecord
     * @param array            $records
     */
    public function addPayment(FeeInvoiceRecord $feeInvoiceRecord, $records): FeeInvoiceRecord
    {
        $pay = Money::of($records['pay'], config('app.currency'));
        $paid = $feeInvoiceRecord->paid;
        $newAmount = $paid->plus($pay);

        if ($this->isPaymentHigherThanDue($feeInvoiceRecord->amount, $newAmount, $feeInvoiceRecord->waiver, $feeInvoiceRecord->fine)) {
            throw new InvalidValueException('Payment cannot be higher than the total amount to pay');
        }

        $feeInvoiceRecord->update([
            'paid' => $newAmount,
        ]);

        return $feeInvoiceRecord;
    }

    public function isPaymentHigherThanDue(Money $amount, Money $paid, Money $waiver, Money $fine): bool
    {
        $due = $amount->plus($fine)->minus($waiver);

        if ($due->isLessThan($paid)) {
            return true;
        }

        return false;
    }
}
