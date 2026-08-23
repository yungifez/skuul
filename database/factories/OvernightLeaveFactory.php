<?php

namespace Database\Factories;

use App\Models\OvernightLeave;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OvernightLeave>
 */
class OvernightLeaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrollment = StudentRecord::factory()->create();

        return [
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'leaves_on' => now()->addDays(2)->toDateString(),
            'returns_on' => now()->addDays(3)->toDateString(),
            'destination' => 'Home with a guardian',
        ];
    }
}
