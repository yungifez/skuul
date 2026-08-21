<?php

namespace App\Actions\Wellbeing;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Enums\SupportCategory;
use App\Enums\SupportPlanStatus;
use App\Exceptions\InvalidValueException;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Models\SupportPlanAction;
use App\Models\SupportPlanNote;
use App\Models\SupportPlanStatusChange;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Write a plan of help down and keep it moving.
 *
 * Health, counselling, accommodations, and interventions follow the same
 * path. What changes is who may read them, which the plan says for itself.
 */
class ManageSupportPlan
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Open a plan for one child.
     *
     * @throws InvalidValueException when the enrollment is closed or the dates are backwards
     */
    public function open(
        StudentRecord $enrollment,
        string $title,
        SupportCategory $category = SupportCategory::Intervention,
        ?string $summary = null,
        CarbonInterface|string|null $startsOn = null,
        CarbonInterface|string|null $reviewOn = null,
        ?User $owner = null,
        ?User $actor = null,
    ): SupportPlan {
        if ($enrollment->status !== EnrollmentStatus::Active) {
            throw new InvalidValueException('A support plan needs an active enrollment.');
        }

        $start = $startsOn === null ? null : Carbon::parse($startsOn);
        $review = $reviewOn === null ? null : Carbon::parse($reviewOn);

        if ($start !== null && $review !== null && $review->lt($start)) {
            throw new InvalidValueException('A plan cannot be reviewed before it starts.');
        }

        return DB::transaction(function () use ($enrollment, $title, $category, $summary, $start, $review, $owner, $actor): SupportPlan {
            $plan = SupportPlan::create([
                'school_id'         => $enrollment->school_id,
                'student_record_id' => $enrollment->id,
                'category'          => $category,
                'title'             => $title,
                'summary'           => $summary,
                'starts_on'         => $start,
                'review_on'         => $review,
                'academic_year_id'  => current_academic_year_id(),
                'created_by'        => $actor === null ? auth()->id() : $actor->id,
                'assigned_to'       => $owner?->id,
            ]);

            $this->auditor->record(
                AuditAction::SupportPlanOpened,
                $plan,
                ['category' => $category->value, 'student_record_id' => $enrollment->id],
                $actor,
            );

            return $plan;
        });
    }

    /**
     * Move the plan to another state.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function changeStatus(SupportPlan $plan, SupportPlanStatus $status, ?User $actor = null, ?string $reason = null): SupportPlan
    {
        $current = $plan->status;

        if ($current === $status) {
            return $plan;
        }

        if (!$current->canMoveTo($status)) {
            throw new InvalidValueException("A support plan cannot move from {$current->value} to {$status->value}.");
        }

        return DB::transaction(function () use ($plan, $current, $status, $actor, $reason): SupportPlan {
            $plan->status = $status;

            if (!$status->isOpen() && $plan->ends_on === null) {
                $plan->ends_on = now();
            }

            $plan->save();

            SupportPlanStatusChange::create([
                'support_plan_id' => $plan->id,
                'from_status'     => $current,
                'to_status'       => $status,
                'reason'          => $reason,
                'changed_by'      => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::SupportPlanStatusChanged,
                $plan,
                ['from' => $current->value, 'to' => $status->value, 'reason' => $reason],
                $actor,
            );

            return $plan;
        });
    }

    /**
     * Add a step the school agrees to take.
     *
     * @throws InvalidValueException when the plan is finished
     */
    public function addAction(
        SupportPlan $plan,
        string $description,
        CarbonInterface|string|null $dueOn = null,
        ?User $assignee = null,
        ?User $actor = null,
    ): SupportPlanAction {
        if (!$plan->status->isOpen()) {
            throw new InvalidValueException('This plan is finished. Reopen it before you add a step.');
        }

        return SupportPlanAction::create([
            'support_plan_id' => $plan->id,
            'description'     => $description,
            'due_on'          => $dueOn === null ? null : Carbon::parse($dueOn),
            'assigned_to'     => $assignee?->id,
            'created_by'      => $actor === null ? auth()->id() : $actor->id,
        ]);
    }

    /**
     * Mark a step done.
     *
     * A step that is already done keeps the time it was first finished.
     */
    public function completeAction(SupportPlanAction $action, ?User $actor = null): SupportPlanAction
    {
        if ($action->completed_at !== null) {
            return $action;
        }

        $action->completed_at = now();
        $action->completed_by = $actor === null ? auth()->id() : $actor->id;
        $action->save();

        return $action;
    }

    /**
     * Write a note about how the plan is going.
     *
     * @throws InvalidValueException when the plan is finished
     */
    public function addNote(SupportPlan $plan, string $body, ?User $actor = null): SupportPlanNote
    {
        if (!$plan->status->isOpen()) {
            throw new InvalidValueException('This plan is finished. Reopen it before you write a note.');
        }

        return SupportPlanNote::create([
            'support_plan_id' => $plan->id,
            'body'            => $body,
            'written_by'      => $actor === null ? auth()->id() : $actor->id,
        ]);
    }
}
