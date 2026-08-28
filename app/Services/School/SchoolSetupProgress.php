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
            SchoolSetupStep::Details->value => $this->detailsComplete($school),
            SchoolSetupStep::Language->value => $this->languageComplete($school),
            SchoolSetupStep::Classes->value => AcademicLevel::inSchool($school->id)->where('is_group', false)->exists(),
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

    /**
     * Confirm that the school details were saved as a complete setup step.
     */
    public function detailsComplete(School $school): bool
    {
        return $school->setup_details_completed_at !== null
            && filled($school->name)
            && filled($school->address)
            && filled($school->country)
            && filled($school->state)
            && filled($school->city)
            && filled($school->postal_code);
    }

    /**
     * Confirm that the school language form was explicitly saved.
     */
    public function languageComplete(School $school): bool
    {
        return $school->operatingProfile()->whereNotNull('setup_completed_at')->exists();
    }
}
