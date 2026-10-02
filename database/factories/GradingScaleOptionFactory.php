<?php

namespace Database\Factories;

use App\Models\GradingScale;
use App\Models\GradingScaleOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradingScaleOption>
 */
class GradingScaleOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grading_scale_id' => GradingScale::factory(),
            'label' => fake()->unique()->word(),
            'points' => fake()->randomFloat(2, 0, 100),
            'position' => fake()->numberBetween(1, 10),
        ];
    }
}
