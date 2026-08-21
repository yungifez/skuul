<?php

namespace Database\Factories;

use App\Models\ExamRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamRecord>
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
            // 1st exam slot reserved for testing
            'exam_slot_id' => $this->faker->numberBetween(2, 9),
            'section_id'   => 1,
            'user_id'      => 4,
            // first subject reserved for testing
            'subject_id'    => $this->faker->numberBetween(1, 10),
            'student_marks' => $this->faker->numberBetween(1, 100),
        ];
    }
}
