<?php

namespace App\Services\School;

use App\Enums\SchoolSetupStep;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\School;

class SchoolSetupProgress
{
    /**
     * @return array{steps: list<array{value: string, label: string, description: string, complete: bool, required: bool}>, current: SchoolSetupStep}
     */
    public function for(School $school): array
    {
        $steps = SchoolSetupStep::cases();
        $completion = [
            SchoolSetupStep::Details->value => filled($school->name) && filled($school->address),
            SchoolSetupStep::Language->value => $school->operatingProfile()->exists(),
            SchoolSetupStep::Classes->value => AcademicLevel::inSchool($school->id)->exists(),
            SchoolSetupStep::AcademicYear->value => AcademicYear::inSchool($school->id)->exists(),
            SchoolSetupStep::Finish->value => false,
        ];

        $current = collect($steps)->first(fn (SchoolSetupStep $step): bool => !$completion[$step->value])
            ?? SchoolSetupStep::Finish;

        return [
            'steps' => array_map(fn (SchoolSetupStep $step): array => [
                'value' => $step->value,
                'label' => $step->label(),
                'description' => $step->description(),
                'complete' => $completion[$step->value],
                'required' => $step !== SchoolSetupStep::Finish,
            ], $steps),
            'current' => $current,
        ];
    }
}
