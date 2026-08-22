<?php

namespace Database\Factories;

use App\Enums\InstructionalModel;
use App\Models\AcademicYear;
use App\Models\InstructionalModelSetting;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstructionalModelSetting>
 */
class InstructionalModelSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id'        => School::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'model'            => InstructionalModel::default(),
            'updated_by'       => null,
        ];
    }

    /**
     * Teach the cycle with one class group all day.
     */
    public function fixedHomeSections(): static
    {
        return $this->state(['model' => InstructionalModel::FixedHomeSections]);
    }

    /**
     * Keep home sections, and let named offerings differ.
     */
    public function hybrid(): static
    {
        return $this->state(['model' => InstructionalModel::Hybrid]);
    }

    /**
     * Give each subject offering its own roster.
     */
    public function subjectBased(): static
    {
        return $this->state(['model' => InstructionalModel::SubjectBasedSchedule]);
    }
}
