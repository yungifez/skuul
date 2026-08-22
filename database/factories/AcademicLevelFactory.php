<?php

namespace Database\Factories;

use App\Models\AcademicLevel;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicLevel>
 */
class AcademicLevelFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var School $school */
        $school = School::query()->first() ?? School::factory()->create();

        return [
            'school_id' => $school->id,
            'name' => fake()->unique()->words(2, true),
            'code' => fake()->unique()->bothify('LVL-###'),
            'position' => fake()->numberBetween(1, 20),
        ];
    }
}
