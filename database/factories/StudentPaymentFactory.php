<?php

namespace Database\Factories;

use App\Models\StudentPayment;
use App\Models\StudentRecord;
use Brick\Money\Money as BrickMoney;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentPayment>
 */
class StudentPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrollment = StudentRecord::factory()->create();

        return [
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'amount' => BrickMoney::ofMinor(fake()->numberBetween(1000, 500000), config('app.currency')),
            'method' => 'cash',
            'reference' => null,
            'received_on' => now()->toDateString(),
        ];
    }

    /**
     * Make a payment that reached the school bank account.
     */
    public function byTransfer(): self
    {
        return $this->state(fn (): array => [
            'method' => 'bank_transfer',
            'reference' => fake()->bothify('TRF-########'),
        ]);
    }
}
