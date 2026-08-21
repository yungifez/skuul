<?php

namespace Database\Factories;

use App\Models\TimetableTimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimetableTimeSlot>
 */
class TimetableTimeSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'timetable_id' => 1,
            'start_time'   => $this->faker->time('H:i'),
            'stop_time'    => $this->faker->time('H:i'),

        ];
    }
}
