<?php

namespace App\Services\Fee;

use App\Exceptions\InvalidValueException;
use App\Models\Fee;
use App\Models\FeeInvoiceRecord;
use Brick\Money\Money;

class FeeInvoiceRecordService
{
    /**
     * Store a new fee invoice record.
     *
     * @param array $records
     *
     * @return $feeInvoiceRecord
     */
    public function storeFeeInvoiceRecord($records)
    {
        $fee = Fee::where('id', $records['fee_id'])->whereRelation('feeCategory', 'school_id', auth()->user()->school_id)->get();
        $feeInvoice = FeeInvoice::where('id', $records['fee_invoice_id'])->whereRelation('user', 'school_id', auth()->user()->school_id)->get();

        if ($fee->isEmpty() || $feeInvoice->isEmpty()) {
            throw new InvalidValueException("The fee you selected doesn't exist");
        }

        FeeInvoiceRecord::create([
            'fee_invoice_id' => $records['fee_invoice_id'],
            'fee_id'         => $records['fee_id'],
            'amount'         => $records['amount'],
            'waiver'         => $records['waiver'] ?? 0,
            'fine'           => $records['fine'] ?? 0,
        ]);
    }

    /**
     * Update a fee invoice record.
     *
     * @param FeeInvoiceRecord $feeInvoiceRecord
     * @param                  $records
     *
     * @return void
     */
    public function updateFeeInvoiceRecord(FeeInvoiceRecord $feeInvoiceRecord, $records)
    {
        $feeInvoiceRecord->update([
            'amount' => $records['amount'],
            'waiver' => $records['waiver'] ?? 0,
            'fine'   => $records['fine'] ?? 0,
        ]);
    }

    /**
     * Delete a fee invoice.
     *
     * @param FeeInvoiceRecord $feeInvoiceRecord
     *
     * @return void
     */
    public function deleteFeeInvoiceRecord(FeeInvoiceRecord $feeInvoiceRecord)
    {
        $feeInvoiceRecord->delete();
    }

    /**
     * Add a new paymeny.
     *
     * @param FeeInvoiceRecord $feeInvoiceRecord
     * @param array            $records
     *
     * @return void
     */
    public function addPayment(FeeInvoiceRecord $feeInvoiceRecord, $records)
    {
        $pay = Money::of($records['pay'], config('app.currency'));

        $paid = $feeInvoiceRecord->paid;

        $newAmount = $paid->plus($pay);

        $feeInvoiceRecord->update([
            'paid' => $newAmount,
        ]);

    }
}
