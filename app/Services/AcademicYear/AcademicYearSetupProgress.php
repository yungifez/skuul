<?php

namespace App\Services\AcademicYear;

use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicYearSetupStep;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Services\Curriculum\InstructionalModelResolver;

class AcademicYearSetupProgress
{
    public function __construct(private InstructionalModelResolver $instructionalModels) {}

    /**
     * @return array{steps: list<array{value: string, label: string, description: string, complete: bool, required: bool}>, current: AcademicYearSetupStep}
     */
    public function for(AcademicYear $academicYear): array
    {
        $steps = AcademicYearSetupStep::cases();
        $completion = [
            AcademicYearSetupStep::Calendar->value => $academicYear->starts_on !== null
                && $academicYear->ends_on !== null
                && $academicYear->topLevelPeriods()->exists(),
            AcademicYearSetupStep::Teaching->value => $this->instructionalModels->isChosen($academicYear, $academicYear->school),
            AcademicYearSetupStep::Structure->value => AcademicLevel::inSchool($academicYear->school_id)->exists()
                && $academicYear->cycleSections()->exists(),
            AcademicYearSetupStep::Subjects->value => CourseOffering::inSchool()
                ->where('academic_year_id', $academicYear->id)
                ->exists(),
            AcademicYearSetupStep::Review->value => $academicYear->status !== AcademicPeriodStatus::Draft,
        ];

        $current = collect($steps)->first(fn (AcademicYearSetupStep $step): bool => !$completion[$step->value])
            ?? AcademicYearSetupStep::Review;

        return [
            'steps' => array_map(fn (AcademicYearSetupStep $step): array => [
                'value' => $step->value,
                'label' => $step->label(),
                'description' => $step->description(),
                'complete' => $completion[$step->value],
                'required' => $step !== AcademicYearSetupStep::Review,
            ], $steps),
            'current' => $current,
        ];
    }
}
