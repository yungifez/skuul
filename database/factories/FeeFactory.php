<?php

namespace Database\Factories;

use App\Models\Fee;
use App\Models\FeeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fee>
 */
class FeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $feeCategory = FeeCategory::query()->where('school_id', 1)->inRandomOrder()->first();

        return [
            'fee_Category_id' => $feeCategory->id,
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
        ];
    }
}
