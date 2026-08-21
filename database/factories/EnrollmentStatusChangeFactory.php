<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\EnrollmentStatusChange;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnrollmentStatusChange>
 */
class EnrollmentStatusChangeFactory extends Factory
{
    protected $model = EnrollmentStatusChange::class;

    /**
     * Define the model's default state.
     *
     * Real changes come from the ChangeEnrollmentStatus action. Use this
     * factory only to set up history a test needs to read.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_record_id' => StudentRecord::factory(),
            'from_status'       => EnrollmentStatus::Active,
            'to_status'         => EnrollmentStatus::Graduated,
            'effective_on'      => now(),
            'changed_by'        => User::factory(),
            'reason'            => $this->faker->sentence(),
        ];
    }

    /**
     * Record a change nobody signed for, such as an import.
     */
    public function withoutActor(): static
    {
        return $this->state(fn (array $attributes): array => [
            'changed_by' => null,
        ]);
    }
}
