<?php

namespace Database\Factories;

use App\Enums\GradingScaleType;
use App\Models\GradingScale;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradingScale>
 */
class GradingScaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var School $school */
        $school = School::query()->first() ?? School::factory()->create();

        return [
            'school_id' => $school->id,
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'scale_type' => GradingScaleType::Points->value,
            'is_active' => true,
        ];
    }
}
