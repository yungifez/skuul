<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Enums\StaffStatus;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffProfile>
 */
class StaffProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'staff_number' => strtoupper($this->faker->bothify('STF-####')),
            'job_title' => $this->faker->jobTitle(),
            'department' => $this->faker->word(),
            'employment_type' => EmploymentType::FullTime,
            'status' => StaffStatus::Active,
            'joined_on' => now()->subYear(),
        ];
    }

    /**
     * Make a person who no longer works here.
     */
    public function left(): static
    {
        return $this->state(fn (): array => [
            'status' => StaffStatus::Left,
            'left_on' => now()->subMonth(),
        ]);
    }
}
