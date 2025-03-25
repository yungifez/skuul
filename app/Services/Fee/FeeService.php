<?php

namespace App\Services\Fee;

use App\Models\Fee;

class FeeService
{
    /**
     * Store a new fee.
     *
     * @param array $records
     */
    public function storeFee($records): Fee
    {
        $fee = Fee::create([
            'name'             => $records['name'],
            'description'      => $records['description'] ?? null,
            'fee_category_id'  => $records['fee_category_id'],
        ]);

        return $fee;
    }

    /**
     * Update a fee.
     *
     * @param Fee   $fee
     * @param array $record
     */
    public function updateFee(Fee $fee, $record): Fee
    {
        $fee->update([
            'name'         => $record['name'],
            'description'  => $record['description'] ?? null,
        ]);

        return $fee;
    }

    /**
     * Delete a fee.
     *
     * @param Fee $fee
     */
    public function deleteFee(Fee $fee): ?bool
    {
        return $fee->delete();
    }
}
