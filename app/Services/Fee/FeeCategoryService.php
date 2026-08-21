<?php

namespace App\Services\Fee;

use App\Models\FeeCategory;

class FeeCategoryService
{
    /**
     * Store a fee category.
     *
     * @param array $record
     */
    public function storeFeeCategory($record): FeeCategory
    {
        $feeCategory = FeeCategory::create([
            'name'        => $record['name'],
            'description' => $record['description'] ?? null,
            'school_id'   => $record['school_id'],
        ]);

        return $feeCategory;
    }

    /**
     * Update a fee category.
     *
     * @param array<string, mixed> $record
     */
    public function updateFeeCategory(FeeCategory $feeCategory, $record): FeeCategory
    {
        $feeCategory->update([
            'name'        => $record['name'],
            'description' => $record['description'] ?? null,
        ]);

        return $feeCategory;
    }

    /**
     * Delete a fee category.
     */
    public function deleteFeeCategory(FeeCategory $feeCategory): ?bool
    {
        return $feeCategory->delete();
    }
}
