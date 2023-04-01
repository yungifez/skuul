<?php

namespace Database\Factories;

use App\Models\Fee;
use App\Models\FeeInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeInvoiceRecord>
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
        $waiver = mt_rand(0, $amount);
        $fine = mt_rand(0, $amount);
        $paid = mt_rand(0, $amount);
        $fee = Fee::query()->offset(rand(1, 7))->whereRelation('feeCategory', 'school_id', 1)->first() ?? Fee::factory()->create();
        $feeInvoice = FeeInvoice::query()->inRandomOrder()->whereRelation('user', 'school_id', 1)->first() ?? FeeInvoice::factory()->create();

        return [
            'fee_id'         => $fee->id,
            'fee_invoice_id' => $feeInvoice->id,
            'amount'         => $amount,
            'waiver'         => $waiver,
            'paid'           => $paid,
            'fine'           => $fine,
        ];
    }
}
