<?php

namespace Database\Factories;

use App\Models\Exam;
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
        $exam = Exam::query()->whereRelation('semester', 'id', 1)->inRandomOrder()->first();

        return [
            'name'        => $this->faker->name,
            'description' => $this->faker->sentence,
            'total_marks' => $this->faker->numberBetween(1, 100),
            'exam_id'     => $exam->id,
        ];
    }
}
