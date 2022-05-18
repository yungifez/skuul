<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamRecord>
 */
class ExamRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'exam_slot_id' => $this->faker->numberBetween(1,10),
            'section_id' => 1,
            'user_id' => 4,
            'subject_id' => $this->faker->numberBetween(1,10),
            'student_marks' => $this->faker->numberBetween(1,100)
        ];
    }
}
