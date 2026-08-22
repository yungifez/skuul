<?php

namespace Database\Factories;

use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicCycleSection>
 */
class AcademicCycleSectionFactory extends Factory
{
    /**
     * Counts the names handed out, so no two sections of one level collide.
     *
     * A cycle keeps one section of each name inside a level, and the database
     * enforces it. Faker's colour list is short enough to repeat, so the count
     * makes each generated name its own.
     */
    private static int $nameSequence = 0;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var School $school */
        $school = School::query()->first() ?? School::factory()->create();
        /** @var AcademicYear $academicYear */
        $academicYear = AcademicYear::query()->where('school_id', $school->id)->first()
            ?? AcademicYear::factory()->create(['school_id' => $school->id]);
        /** @var AcademicLevel $academicLevel */
        $academicLevel = AcademicLevel::query()->where('school_id', $school->id)->first()
            ?? AcademicLevel::factory()->create(['school_id' => $school->id]);

        return [
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'name' => fake()->colorName().' '.++self::$nameSequence,
            'stream' => fake()->randomElement(['Morning', 'Afternoon', null]),
            'capacity' => fake()->numberBetween(15, 60),
            'position' => fake()->numberBetween(1, 10),
        ];
    }
}
