<?php

namespace App\Services\Fee;

use App\Exceptions\InvalidValueException;
use App\Models\Fee;

class FeeService
{
    /**
     * Store a new fee.
     *
     * @param  array  $records
     */
    public function storeFee($records): Fee
    {
        $fee = Fee::create([
            'name' => $records['name'],
            'description' => $records['description'] ?? null,
            'fee_category_id' => $records['fee_category_id'],
        ]);

        return $fee;
    }

    /**
     * Update a fee.
     *
     * @param  array  $record
     */
    public function updateFee(Fee $fee, $record): Fee
    {
        $fee->update([
            'name' => $record['name'],
            'description' => $record['description'] ?? null,
        ]);

        return $fee;
    }

    /**
     * Delete a fee.
     *
     * A fee that is on an invoice cannot go. The delete is a soft delete, so
     * the row stays but the relation reads null, and both invoice screens
     * read the fee name straight off it.
     */
    public function deleteFee(Fee $fee): ?bool
    {
        if ($fee->feeInvoiceRecords()->exists()) {
            throw new InvalidValueException('This fee is on an invoice, so it cannot be deleted. Take it off the invoices that carry it first.');
        }

        return $fee->delete();
    }
}
