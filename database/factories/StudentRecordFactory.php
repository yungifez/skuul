<?php

namespace Database\Factories;

use App\Enums\AcademicStructureStatus;
use App\Enums\EnrollmentStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<StudentRecord>
 */
class StudentRecordFactory extends Factory
{
    protected $model = StudentRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $student = User::query()->findOrFail(User::factory()->create()->getKey());
        $school = School::query()->first() ?? School::query()->findOrFail(School::factory()->create()->getKey());
        $academicYear = AcademicYear::query()->where('school_id', $school->id)->first()
            ?? AcademicYear::query()->findOrFail(AcademicYear::factory()->create(['school_id' => $school->id])->getKey());
        $academicLevel = AcademicLevel::query()->where('school_id', $school->id)->first()
            ?? AcademicLevel::query()->findOrFail(AcademicLevel::factory()->create(['school_id' => $school->id])->getKey());
        $academicCycleSection = AcademicCycleSection::query()
            ->where('school_id', $school->id)
            ->where('academic_year_id', $academicYear->id)
            ->first()
            ?? AcademicCycleSection::query()->findOrFail(AcademicCycleSection::factory()->create([
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'academic_level_id' => $academicLevel->id,
                'status' => AcademicStructureStatus::Active,
            ])->getKey());
        $student->assignRole('student');

        return [
            'user_id' => $student->id,
            'school_id' => $school->id,
            'academic_cycle_section_id' => $academicCycleSection->id,
            'admission_date' => $this->faker->date(),
            'status' => EnrollmentStatus::Active,
            'admission_number' => Str::random(10),
        ];
    }
}
