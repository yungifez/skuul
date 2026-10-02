<?php

namespace Database\Factories;

use App\Enums\SchoolMembershipStatus;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolMembership>
 */
class SchoolMembershipFactory extends Factory
{
    protected $model = SchoolMembership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'school_id' => School::factory(),
            'status' => SchoolMembershipStatus::Active,
            'is_primary' => true,
            'joined_at' => now(),
        ];
    }

    /**
     * Indicate that the person left this school.
     */
    public function ended(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => SchoolMembershipStatus::Ended,
            'is_primary' => false,
            'ended_at' => now(),
        ]);
    }

    /**
     * Indicate that access to this school is stopped.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => SchoolMembershipStatus::Suspended,
        ]);
    }
}
