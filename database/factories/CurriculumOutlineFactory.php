<?php

namespace Database\Factories;

use App\Models\CurriculumOutline;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CurriculumOutline>
 */
class CurriculumOutlineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'subject_id' => Subject::factory(),
            'academic_level_id' => null,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}
