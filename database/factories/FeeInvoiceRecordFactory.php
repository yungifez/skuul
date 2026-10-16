<?php

namespace Database\Factories;

use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeeInvoiceRecord>
 */
class FeeInvoiceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = mt_rand(1000, 10000000);
        $waiver = mt_rand(0, 100);
        $fine = mt_rand(0, 100);
        $fee = Fee::query()->offset(rand(1, 7))->whereRelation('feeCategory', 'school_id', 1)->first() ?? Fee::factory()->create();
        $feeInvoice = FeeInvoice::query()->inRandomOrder()->whereHas('user', fn ($user) => $user->ofSchool(1))->first() ?? FeeInvoice::factory()->create();

        return [
            'fee_id' => $fee->id,
            'fee_invoice_id' => $feeInvoice->id,
            'amount' => $amount,
            'waiver' => $waiver,
            'fine' => $fine,
        ];
    }
}
