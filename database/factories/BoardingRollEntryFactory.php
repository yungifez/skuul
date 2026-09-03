<?php

namespace Database\Factories;

use App\Enums\BoardingRollEntryStatus;
use App\Models\BoardingRoll;
use App\Models\BoardingRollEntry;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardingRollEntry>
 */
class BoardingRollEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roll = BoardingRoll::factory()->create();
        $student = StudentRecord::factory()->create(['school_id' => $roll->school_id]);

        return [
            'school_id' => $roll->school_id,
            'boarding_roll_id' => $roll->id,
            'student_record_id' => $student->id,
            'status' => BoardingRollEntryStatus::NotRecorded,
        ];
    }
}
