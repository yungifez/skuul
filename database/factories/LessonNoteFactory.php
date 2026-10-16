<?php

namespace Database\Factories;

use App\Enums\LessonNoteStatus;
use App\Models\CourseOffering;
use App\Models\LessonNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonNote>
 */
class LessonNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'syllabus_topic_id' => null,
            'academic_cycle_section_id' => null,
            'user_id' => User::factory(),
            'week' => fake()->numberBetween(1, 12),
            'objectives' => fake()->sentence(),
            'activities' => fake()->paragraph(),
            'evaluation' => fake()->sentence(),
            'status' => LessonNoteStatus::Draft,
        ];
    }

    /**
     * Indicate that the note waits for review.
     */
    public function submitted(): static
    {
        return $this->state(fn (): array => [
            'status' => LessonNoteStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }
}
