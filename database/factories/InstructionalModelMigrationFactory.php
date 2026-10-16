<?php

namespace Database\Factories;

use App\Enums\InstructionalModel;
use App\Models\AcademicYear;
use App\Models\InstructionalModelMigration;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstructionalModelMigration>
 */
class InstructionalModelMigrationFactory extends Factory
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
            'academic_year_id' => AcademicYear::factory(),
            'from_model' => InstructionalModel::FixedHomeSections,
            'to_model' => InstructionalModel::Hybrid,
            'reason' => 'The campus agreed to combine two sections for music.',
            'impact' => null,
            'migrated_by' => null,
        ];
    }
}
