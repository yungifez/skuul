<?php

namespace Database\Factories;

use App\Enums\CourseOfferingStatus;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseOffering>
 */
class CourseOfferingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var School $school */
        $school = School::query()->first() ?? School::factory()->create();
        /** @var AcademicYear $academicYear */
        $academicYear = AcademicYear::query()->where('school_id', $school->id)->first()
            ?? AcademicYear::factory()->create(['school_id' => $school->id]);
        /** @var AcademicPeriod $academicPeriod */
        $academicPeriod = AcademicPeriod::query()
            ->where('academic_year_id', $academicYear->id)
            ->first()
            ?? AcademicPeriod::factory()->create([
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
            ]);
        /** @var AcademicLevel $academicLevel */
        $academicLevel = AcademicLevel::query()->where('school_id', $school->id)->first()
            ?? AcademicLevel::factory()->create(['school_id' => $school->id]);
        /** @var Subject $subject */
        $subject = Subject::query()
            ->where('school_id', $school->id)
            ->first()
            ?? Subject::factory()->create([
                'school_id' => $school->id,
            ]);

        return [
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'subject_id' => $subject->id,
            'academic_level_id' => $academicLevel->id,
            'status' => CourseOfferingStatus::Draft,
            'planned_periods_per_week' => fake()->numberBetween(1, 10),
            'capacity' => fake()->numberBetween(10, 60),
        ];
    }
}
