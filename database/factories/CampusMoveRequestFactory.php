<?php

namespace Database\Factories;

use App\Enums\AcademicStructureStatus;
use App\Enums\CampusMoveStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CampusMoveRequest;
use App\Models\School;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CampusMoveRequest>
 */
class CampusMoveRequestFactory extends Factory
{
    protected $model = CampusMoveRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrollment = StudentRecord::query()->first() ?? StudentRecord::factory()->create();
        $source = School::query()->findOrFail($enrollment->school_id);
        $destination = School::factory()->create(['organization_id' => $source->organization_id]);

        return [
            'student_record_id' => $enrollment->id,
            'from_school_id' => $source->id,
            'to_school_id' => $destination->id,
            'academic_cycle_section_id' => fn (array $attributes) => $this->cycleSectionFor($attributes['to_school_id'])->id,
            'status' => CampusMoveStatus::Requested,
            'reason' => fake()->sentence(),
            'effective_on' => now()->toDateString(),
        ];
    }

    /**
     * Make an active cycle section on the receiving campus.
     */
    private function cycleSectionFor(int $schoolId): AcademicCycleSection
    {
        $academicYear = AcademicYear::query()->where('school_id', $schoolId)->first()
            ?? AcademicYear::factory()->create(['school_id' => $schoolId]);
        $academicLevel = AcademicLevel::query()->where('school_id', $schoolId)->first()
            ?? AcademicLevel::factory()->create(['school_id' => $schoolId]);

        return AcademicCycleSection::factory()->create([
            'school_id' => $schoolId,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }
}
