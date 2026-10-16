<?php

namespace Database\Factories;

use App\Enums\AdmissionWaitlistStatus;
use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdmissionWaitlistEntry>
 */
class AdmissionWaitlistEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $section = AcademicCycleSection::factory()->create(['capacity' => 10]);

        return [
            'school_id' => $section->school_id,
            'academic_year_id' => $section->academic_year_id,
            'academic_cycle_section_id' => $section->id,
            'user_id' => User::factory(),
            'status' => AdmissionWaitlistStatus::Pending,
            'priority' => 0,
            'position' => 1,
        ];
    }
}
