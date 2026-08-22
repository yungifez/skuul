<?php

namespace Database\Factories;

use App\Models\Timetable;
use App\Models\TimetableSubstitution;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimetableSubstitution>
 */
class TimetableSubstitutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'timetable_id'           => Timetable::factory(),
            'timetable_time_slot_id' => TimetableTimeSlot::factory(),
            'weekday_id'             => fn (): int => Weekday::query()->firstOrCreate(['name' => 'Monday'])->id,
            'replacement_teacher_id' => User::factory(),
            'substituted_on'         => $this->faker->date(),
            'reason'                 => $this->faker->sentence(),
            'approved_by'            => User::factory(),
        ];
    }
}
