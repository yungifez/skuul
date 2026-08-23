<?php

namespace Database\Factories;

use App\Models\BoardingPlace;
use App\Models\DormitoryBed;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardingPlace>
 */
class BoardingPlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrollment = StudentRecord::factory()->create();
        $bed = DormitoryBed::factory()->create(['school_id' => $enrollment->school_id]);

        return [
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'dormitory_bed_id' => $bed->id,
            'effective_on' => now()->toDateString(),
        ];
    }
}
