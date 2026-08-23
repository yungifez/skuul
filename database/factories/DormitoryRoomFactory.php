<?php

namespace Database\Factories;

use App\Models\Dormitory;
use App\Models\DormitoryRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DormitoryRoom>
 */
class DormitoryRoomFactory extends Factory
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
            'name' => 'Room '.fake()->unique()->numberBetween(1, 400),
        ];
    }
}
