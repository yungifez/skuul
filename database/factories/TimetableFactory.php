<?php

namespace Database\Factories;

use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Timetable>
 */
class TimetableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cycleSection = AcademicCycleSection::factory()->create();
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $cycleSection->school_id,
            'academic_year_id' => $cycleSection->academic_year_id,
        ]);

        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'academic_cycle_section_id' => $cycleSection->id,
            'academic_period_id' => $academicPeriod->id,
        ];
    }
}
