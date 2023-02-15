<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester>
 */
class SemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id'               => $this->faker->numberBetween(1, 10000),
            'name'             => $this->faker->unique()->word,
            'academic_year_id' => 1,
            'school_id'        => 1,
        ];
    }
}
