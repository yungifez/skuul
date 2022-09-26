<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamSlot>
 */
class ExamSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'        => $this->faker->name,
            'description' => $this->faker->sentence,
            'total_marks' => $this->faker->numberBetween(1, 1000),
            'exam_id'     => 1,
        ];
    }
}
