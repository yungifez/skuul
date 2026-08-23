<?php

namespace Database\Factories;

use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DormitoryBed>
 */
class DormitoryBedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $room = DormitoryRoom::factory()->create();

        return [
            'school_id' => $room->school_id,
            'dormitory_room_id' => $room->id,
            'name' => 'Bed '.fake()->unique()->numberBetween(1, 2000),
        ];
    }
}
