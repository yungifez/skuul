<?php

namespace Database\Factories;

use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SyllabusTopic>
 */
class SyllabusTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'syllabus_id' => Syllabus::factory(),
            'week' => fake()->numberBetween(1, 12),
            'position' => 0,
            'title' => fake()->sentence(3),
            'objectives' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'resources' => null,
        ];
    }
}
