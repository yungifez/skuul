<?php

namespace Database\Factories;

use App\Enums\SupervisionRole;
use App\Models\BoardingSupervision;
use App\Models\Dormitory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardingSupervision>
 */
class BoardingSupervisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dormitory = Dormitory::factory()->create();

        return [
            'school_id' => $dormitory->school_id,
            'dormitory_id' => $dormitory->id,
            'user_id' => User::factory(),
            'role' => SupervisionRole::Warden,
            'starts_on' => now()->toDateString(),
        ];
    }
}
