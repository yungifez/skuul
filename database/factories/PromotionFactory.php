<?php

namespace Database\Factories;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Promotion;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * The two cycle sections are lazy, so a caller that names its own sections
     * never pays for throwaway rows in the wrong school.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $school = School::query()->first() ?? School::factory()->create();
        $academicYear = AcademicYear::query()->where('school_id', $school->id)->first()
            ?? AcademicYear::factory()->create(['school_id' => $school->id]);

        return [
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'source_academic_cycle_section_id' => fn (array $attributes) => $this->cycleSection($attributes)->id,
            'destination_academic_cycle_section_id' => fn (array $attributes) => $this->cycleSection($attributes)->id,
            'students' => [4],
        ];
    }

    /**
     * Make one active cycle section in the school and year of the promotion.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function cycleSection(array $attributes): AcademicCycleSection
    {
        $schoolId = $attributes['school_id'];
        $academicLevel = AcademicLevel::query()->where('school_id', $schoolId)->first()
            ?? AcademicLevel::factory()->create(['school_id' => $schoolId]);

        return AcademicCycleSection::factory()->create([
            'school_id' => $schoolId,
            'academic_year_id' => $attributes['academic_year_id'],
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }
}
