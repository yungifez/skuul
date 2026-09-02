<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\SchoolOperatingProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolOperatingProfile>
 */
class SchoolOperatingProfileFactory extends Factory
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
            'preset' => SchoolOperatingProfile::DEFAULT_PRESET,
            'labels' => SchoolOperatingProfile::labelsFor(SchoolOperatingProfile::DEFAULT_PRESET),
        ];
    }
}
