<?php

namespace Database\Factories;

use App\Enums\RosterMode;
use App\Models\AcademicYear;
use App\Models\InstructionalModelException;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstructionalModelException>
 */
class InstructionalModelExceptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $academicYear = AcademicYear::factory()->create();

        return [
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'subject_id' => Subject::factory()->create(['school_id' => $academicYear->school_id])->id,
            'roster_mode' => RosterMode::CombinedHomeSections,
            'reason' => 'One combined music class for the whole level.',
        ];
    }
}
