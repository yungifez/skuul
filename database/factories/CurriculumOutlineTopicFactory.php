<?php

namespace Database\Factories;

use App\Models\CurriculumOutline;
use App\Models\CurriculumOutlineTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CurriculumOutlineTopic>
 */
class CurriculumOutlineTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'curriculum_outline_id' => CurriculumOutline::factory(),
            'week' => fake()->numberBetween(1, 12),
            'position' => 0,
            'title' => fake()->sentence(3),
            'objectives' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'resources' => null,
        ];
    }
}
