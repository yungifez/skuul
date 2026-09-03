<?php

namespace Database\Factories;

use App\Enums\BoardingRollType;
use App\Models\BoardingRoll;
use App\Models\Dormitory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardingRoll>
 */
class BoardingRollFactory extends Factory
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
            'type' => BoardingRollType::Evening,
            'taken_on' => now()->toDateString(),
        ];
    }
}
