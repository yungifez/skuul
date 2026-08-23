<?php

namespace Database\Factories;

use App\Models\FeeInvoiceRecord;
use App\Models\PaymentAllocation;
use App\Models\StudentPayment;
use Brick\Money\Money as BrickMoney;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentAllocation>
 */
class PaymentAllocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $line = FeeInvoiceRecord::factory()->create();

        return [
            'student_payment_id' => StudentPayment::factory(),
            'fee_invoice_id' => $line->fee_invoice_id,
            'fee_invoice_record_id' => $line->id,
            'amount' => BrickMoney::ofMinor(fake()->numberBetween(100, 10000), config('app.currency')),
        ];
    }
}
