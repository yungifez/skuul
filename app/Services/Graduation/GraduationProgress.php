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
     *     requirements: array<int, array{requirement_id: int, description: string, state: string, credits: int, percentage: float|null, is_required: bool, is_negated: bool}>,
     *     stages: array<int, array<string, mixed>>,
     *     credits_earned: int,
     *     credits_required: int|null,
     *     is_complete: bool
     * }
     */
    public function for(GraduationPlan $plan, StudentRecord $enrollment): array
    {
        $result = $this->evaluatePlan($plan, $enrollment);

        $creditsAreEnough = !$plan->uses_credits
            || $plan->required_credits === null
            || $result['credits_earned'] >= $plan->required_credits;

        return [
            'requirements' => $result['requirements'],
            'stages' => $result['stages'],
            'credits_earned' => $result['credits_earned'],
            'credits_required' => $plan->uses_credits ? $plan->required_credits : null,
            'is_complete' => $result['is_complete'] && $creditsAreEnough,
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
     * Evaluate one stage and all of its nested stages.
     *
     * @return array{requirements: array<int, array{requirement_id: int, description: string, state: string, credits: int, percentage: float|null, is_required: bool, is_negated: bool}>, stages: array<int, array<string, mixed>>, credits_earned: int, is_complete: bool}
     */
    private function evaluatePlan(GraduationPlan $plan, StudentRecord $enrollment): array
    {
        $requirements = $plan->requirements()->get();
        $exempt = GraduationExemption::query()
            ->where('student_record_id', $enrollment->id)
            ->whereIn('graduation_requirement_id', $requirements->pluck('id'))
            ->pluck('graduation_requirement_id')
            ->all();
        $conditions = [];
        $lines = [];
        $earned = 0;

        foreach ($requirements as $requirement) {
            $percentage = $this->resultFor($requirement, $enrollment);
            $state = $this->stateOf($requirement, $percentage, in_array($requirement->id, $exempt, true));
            $isMet = in_array($state, ['met', 'exempt'], true);

            if ($isMet) {
                $earned += $requirement->credits;
            }

            if ($requirement->is_required) {
                $conditions[] = $requirement->is_negated ? !$isMet : $isMet;
            }

            $lines[] = [
                'requirement_id' => $requirement->id,
                'description' => $requirement->description,
                'state' => $state,
                'credits' => $requirement->credits,
                'percentage' => $percentage,
                'is_required' => $requirement->is_required,
                'is_negated' => $requirement->is_negated,
            ];
        }

        $stages = [];
        foreach ($plan->children()->where('is_active', true)->get() as $child) {
            $childResult = $this->evaluatePlan($child, $enrollment);
            $childIsComplete = $child->is_negated ? !$childResult['is_complete'] : $childResult['is_complete'];
            $conditions[] = $childIsComplete;
            $earned += $childResult['credits_earned'];
            $stages[] = [
                'plan_id' => $child->id,
                'name' => $child->name,
                'operator' => $child->completion_operator,
                'is_negated' => $child->is_negated,
                'is_complete' => $childIsComplete,
                'requirements' => $childResult['requirements'],
                'stages' => $childResult['stages'],
            ];
        }

        $creditsAreEnough = !$plan->uses_credits
            || $plan->required_credits === null
            || $earned >= $plan->required_credits;

        return [
            'requirements' => $lines,
            'stages' => $stages,
            'credits_earned' => $earned,
            'is_complete' => $this->conditionsMatch($conditions, $plan->completion_operator) && $creditsAreEnough,
        ];
    }

    /**
     * Apply a stage's ALL or ANY rule to its required items.
     *
     * @param  array<int, bool>  $conditions
     */
    private function conditionsMatch(array $conditions, string $operator): bool
    {
        if ($conditions === []) {
            return true;
        }

        return $operator === 'any'
            ? in_array(true, $conditions, true)
            : !in_array(false, $conditions, true);
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
            ->approved()
            ->where('student_record_id', $enrollment->id)
            ->whereHas('courseOffering', fn ($query) => $query->where('subject_id', $requirement->subject_id))
            ->with('courseOffering.academicYear')
            ->get()
            ->sortByDesc(fn (ResultSnapshot $snapshot): string => sprintf(
                '%s-%05d',
                $snapshot->courseOffering?->academicYear?->starts_on?->toDateString() ?? '',
                $snapshot->revision,
            ))
            ->first();

        return $snapshot?->percentage;
    }
}
