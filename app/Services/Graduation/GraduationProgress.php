<?php

namespace App\Services\Graduation;

use App\Models\GraduationExemption;
use App\Models\GraduationPlan;
use App\Models\GraduationRequirement;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;

/**
 * Say how far a student is through a graduation plan.
 *
 * Only a published result counts. Work in the gradebook is not a result yet,
 * so a plan never reads a mark a family has not seen.
 */
class GraduationProgress
{
    /**
     * Work out where one student stands.
     *
     * @return array{
     *     requirements: array<int, array{requirement_id: int, description: string, state: string, credits: int, percentage: float|null}>,
     *     credits_earned: int,
     *     credits_required: int|null,
     *     is_complete: bool
     * }
     */
    public function for(GraduationPlan $plan, StudentRecord $enrollment): array
    {
        $requirements = $plan->requirements()->get();
        $exempt = GraduationExemption::query()
            ->where('student_record_id', $enrollment->id)
            ->whereIn('graduation_requirement_id', $requirements->pluck('id'))
            ->pluck('graduation_requirement_id')
            ->all();

        $lines = [];
        $earned = 0;
        $everythingRequiredIsDone = true;

        foreach ($requirements as $requirement) {
            $percentage = $this->resultFor($requirement, $enrollment);
            $state = $this->stateOf($requirement, $percentage, in_array($requirement->id, $exempt, true));

            if (in_array($state, ['met', 'exempt'], true)) {
                $earned += $requirement->credits;
            } elseif ($requirement->is_required) {
                $everythingRequiredIsDone = false;
            }

            $lines[] = [
                'requirement_id' => $requirement->id,
                'description' => $requirement->description,
                'state' => $state,
                'credits' => $requirement->credits,
                'percentage' => $percentage,
            ];
        }

        $creditsAreEnough = !$plan->uses_credits
            || $plan->required_credits === null
            || $earned >= $plan->required_credits;

        return [
            'requirements' => $lines,
            'credits_earned' => $earned,
            'credits_required' => $plan->uses_credits ? $plan->required_credits : null,
            'is_complete' => $everythingRequiredIsDone && $creditsAreEnough,
        ];
    }

    /**
     * Check if the student has finished the plan.
     */
    public function isComplete(GraduationPlan $plan, StudentRecord $enrollment): bool
    {
        return $this->for($plan, $enrollment)['is_complete'];
    }

    /**
     * Get what one requirement is worth to this student.
     *
     * A requirement that names no subject cannot be judged from results. The
     * school marks it by excusing the student or by recording it elsewhere.
     */
    private function stateOf(GraduationRequirement $requirement, ?float $percentage, bool $isExempt): string
    {
        if ($isExempt) {
            return 'exempt';
        }

        if ($requirement->subject_id === null) {
            return 'not_judged';
        }

        if ($percentage === null) {
            return 'no_result';
        }

        return $percentage >= $requirement->pass_mark ? 'met' : 'not_met';
    }

    /**
     * Get the newest published mark for the subject the requirement names.
     */
    private function resultFor(GraduationRequirement $requirement, StudentRecord $enrollment): ?float
    {
        if ($requirement->subject_id === null) {
            return null;
        }

        $snapshot = ResultSnapshot::query()
            ->where('student_record_id', $enrollment->id)
            ->where('subject_id', $requirement->subject_id)
            ->orderByDesc('academic_year_id')
            ->latestRevision()
            ->first();

        return $snapshot?->percentage;
    }
}
