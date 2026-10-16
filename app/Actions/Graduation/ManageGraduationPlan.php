<?php

namespace App\Actions\Graduation;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\GraduationExemption;
use App\Models\GraduationPlan;
use App\Models\GraduationRequirement;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Write a graduation plan, its stages and requirements, and excuse a learner
 * from one requirement.
 *
 * A plan and its stages share one list of names in a school, so a stage can
 * be found by name in the plan list and the portal.
 */
class ManageGraduationPlan
{
    public const array OPERATORS = ['all', 'any', 'at_least', 'at_least_credits'];

    public function __construct(private RecordAuditEvent $audit) {}

    /**
     * Write a plan in the working school.
     *
     * @param  array{name: string, description: string|null, cohort_id: int|null, completion_operator: string, required_count: int|null, uses_credits: bool, required_credits: int|null}  $attributes
     *
     * @throws InvalidValueException when the name is taken or the rule is incomplete
     */
    public function create(array $attributes, User $actor): GraduationPlan
    {
        $attributes = $this->settleTheRule($attributes);

        return $this->withNameLock(function () use ($attributes, $actor): GraduationPlan {
            $this->refuseATakenName($attributes['name']);

            $plan = GraduationPlan::create([...$attributes, 'school_id' => current_school_id()]);
            $this->audit->record(AuditAction::GraduationPlanChanged, $plan, ['created' => true], $actor);

            return $plan;
        });
    }

    /**
     * Change a plan or a stage.
     *
     * @param  array{name: string, description: string|null, completion_operator: string, required_count: int|null, uses_credits: bool, required_credits: int|null, is_active: bool}  $attributes
     *
     * @throws InvalidValueException when the name is taken or the rule is incomplete
     */
    public function update(GraduationPlan $plan, array $attributes, User $actor): GraduationPlan
    {
        $attributes = $this->settleTheRule($attributes);

        return $this->withNameLock(function () use ($plan, $attributes, $actor): GraduationPlan {
            $plan = GraduationPlan::query()->lockForUpdate()->findOrFail($plan->getKey());
            $this->refuseATakenName($attributes['name'], $plan);

            $plan->fill($attributes);

            if (!$plan->isDirty()) {
                return $plan;
            }

            $changed = array_keys($plan->getDirty());
            $plan->save();
            $this->audit->record(AuditAction::GraduationPlanChanged, $plan, ['changed' => $changed], $actor);

            return $plan;
        });
    }

    /**
     * Add a nested stage below a plan or another stage.
     *
     * @param  array{name: string, description: string|null, completion_operator: string, required_count: int|null, required_credits: int|null, is_negated: bool}  $attributes
     *
     * @throws InvalidValueException when the name is taken or the rule is incomplete
     */
    public function addStage(GraduationPlan $parent, array $attributes, User $actor): GraduationPlan
    {
        $attributes = $this->settleTheRule([...$attributes, 'uses_credits' => $attributes['completion_operator'] === 'at_least_credits']);

        return $this->withNameLock(function () use ($parent, $attributes, $actor): GraduationPlan {
            $parent = GraduationPlan::query()->lockForUpdate()->findOrFail($parent->getKey());
            $this->refuseATakenName($attributes['name']);

            $stage = GraduationPlan::create([
                ...$attributes,
                'school_id' => $parent->school_id,
                'parent_id' => $parent->id,
                'position' => ($parent->children()->max('position') ?? -1) + 1,
                'cohort_id' => $parent->cohort_id,
            ]);

            $this->audit->record(AuditAction::GraduationPlanChanged, $parent, ['stage_added' => $stage->id], $actor);

            return $stage;
        });
    }

    /**
     * Add something a learner must finish.
     *
     * @param  array{description: string, subject_id: int|null, credits: int, pass_mark: float, is_required: bool, is_negated: bool}  $attributes
     */
    public function addRequirement(GraduationPlan $plan, array $attributes, User $actor): GraduationRequirement
    {
        return DB::transaction(function () use ($plan, $attributes, $actor): GraduationRequirement {
            $requirement = GraduationRequirement::create([...$attributes, 'graduation_plan_id' => $plan->id]);
            $this->audit->record(AuditAction::GraduationPlanChanged, $plan, ['requirement_added' => $requirement->id], $actor);

            return $requirement;
        });
    }

    /**
     * Take a requirement off the plan, with every excusal from it.
     */
    public function removeRequirement(GraduationRequirement $requirement, User $actor): void
    {
        DB::transaction(function () use ($requirement, $actor): void {
            $requirement = GraduationRequirement::query()->lockForUpdate()->findOrFail($requirement->getKey());
            $excused = $requirement->exemptions()->pluck('student_record_id')->all();

            $requirement->delete();
            $this->audit->record(AuditAction::GraduationPlanChanged, $requirement->graduationPlan, [
                'requirement_removed' => $requirement->id,
                'description' => $requirement->description,
                'excusals_removed_for' => $excused,
            ], $actor);
        });
    }

    /**
     * Excuse one learner from one requirement, or give a new reason.
     *
     * @throws InvalidValueException when the learner is in another school
     */
    public function excuse(GraduationRequirement $requirement, StudentRecord $enrollment, string $reason, User $actor): GraduationExemption
    {
        $plan = $requirement->graduationPlan()->firstOrFail();

        if ($enrollment->school_id !== $plan->school_id) {
            throw new InvalidValueException('Only a learner of this school can be excused.');
        }

        return DB::transaction(function () use ($requirement, $enrollment, $reason, $actor, $plan): GraduationExemption {
            GraduationRequirement::query()->lockForUpdate()->findOrFail($requirement->getKey());

            $exemption = GraduationExemption::updateOrCreate(
                ['graduation_requirement_id' => $requirement->id, 'student_record_id' => $enrollment->id],
                ['reason' => $reason, 'granted_by' => $actor->id],
            );

            $this->audit->record(AuditAction::GraduationExemptionChanged, $plan, [
                'requirement_id' => $requirement->id,
                'student_record_id' => $enrollment->id,
                'reason' => $reason,
            ], $actor);

            return $exemption;
        });
    }

    /**
     * Take an excusal back.
     */
    public function takeBack(GraduationExemption $exemption, User $actor): void
    {
        DB::transaction(function () use ($exemption, $actor): void {
            $plan = $exemption->graduationRequirement()->firstOrFail()->graduationPlan()->firstOrFail();

            $exemption->delete();
            $this->audit->record(AuditAction::GraduationExemptionChanged, $plan, [
                'requirement_id' => $exemption->graduation_requirement_id,
                'student_record_id' => $exemption->student_record_id,
                'taken_back' => true,
            ], $actor);
        });
    }

    /**
     * Keep only the numbers the chosen rule reads, and refuse a rule without them.
     *
     * @template T of array{completion_operator: string, required_count: int|null, uses_credits: bool, required_credits: int|null}
     *
     * @param  T  $attributes
     * @return T
     *
     * @throws InvalidValueException
     */
    private function settleTheRule(array $attributes): array
    {
        if (!in_array($attributes['completion_operator'], self::OPERATORS, true)) {
            throw new InvalidValueException('Choose how the items count.');
        }

        $attributes['uses_credits'] = $attributes['uses_credits'] || $attributes['completion_operator'] === 'at_least_credits';

        if ($attributes['completion_operator'] !== 'at_least') {
            $attributes['required_count'] = null;
        } elseif ($attributes['required_count'] === null) {
            throw new InvalidValueException('Say how many items are needed.');
        }

        if (!$attributes['uses_credits']) {
            $attributes['required_credits'] = null;
        } elseif ($attributes['required_credits'] === null) {
            throw new InvalidValueException('A plan that counts credits must say how many are needed.');
        }

        return $attributes;
    }

    /**
     * @template T
     *
     * @param  callable(): T  $write
     * @return T
     *
     * @throws InvalidValueException
     */
    private function withNameLock(callable $write): mixed
    {
        try {
            return DB::transaction(function () use ($write): mixed {
                School::query()->lockForUpdate()->findOrFail(current_school_id());

                return $write();
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('This school already has a plan or stage with that name.');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function refuseATakenName(string $name, ?GraduationPlan $ignore = null): void
    {
        $isTaken = GraduationPlan::query()
            ->inSchool()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($ignore !== null, fn ($query) => $query->whereKeyNot($ignore->getKey()))
            ->exists();

        if ($isTaken) {
            throw new InvalidValueException('This school already has a plan or stage with that name.');
        }
    }
}
