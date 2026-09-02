<?php

namespace Database\Factories;

use App\Enums\SchoolSetupPhaseStatus;
use App\Models\School;
use App\Models\SchoolSetupPhase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolSetupPhase>
 */
class SchoolSetupPhaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'academic_year_id' => null,
            'phase_key' => 'daily-work-v1:academic-year-none',
            'status' => SchoolSetupPhaseStatus::InProgress,
            'completed_at' => null,
            'acknowledged_at' => null,
            'acknowledged_by' => null,
        ];
    }
}
