<?php

namespace App\Livewire;

use App\Actions\Wellbeing\ManageSupportPlan;
use App\Enums\SupportPlanStatus;
use App\Exceptions\InvalidValueException;
use App\Models\SupportPlan;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use App\Traits\ValidatesSchoolMembership;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Show one support plan and let the people who run it move it along.
 *
 * Steps, notes and moves are appended. Nothing here edits what somebody
 * wrote before.
 */
class ShowSupportPlan extends Component
{
    use ListsSchoolPeople;
    use ValidatesSchoolMembership;

    public SupportPlan $plan;

    public string $nextStatus = '';

    public string $statusReason = '';

    public string $actionDescription = '';

    public ?int $actionAssigneeId = null;

    public string $actionDueOn = '';

    public string $noteBody = '';

    public function mount(SupportPlan $plan): void
    {
        Gate::authorize('view', $plan);

        $this->plan = $plan;
        $this->nextStatus = $plan->status->allowedNext()[0]->value ?? '';
    }

    /**
     * Move the plan to another state.
     */
    public function changeStatus(ManageSupportPlan $manageSupportPlan): void
    {
        Gate::authorize('update', $this->plan);

        $this->validate([
            'nextStatus' => ['required', Rule::enum(SupportPlanStatus::class)],
            'statusReason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $manageSupportPlan->changeStatus(
                plan: $this->plan,
                status: SupportPlanStatus::from($this->nextStatus),
                actor: auth()->user(),
                reason: $this->statusReason === '' ? null : $this->statusReason,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('nextStatus', $exception->getMessage());

            return;
        }

        $this->plan->refresh();
        $this->reset('statusReason');
        $this->nextStatus = $this->plan->status->allowedNext()[0]->value ?? '';
    }

    /**
     * Add a step the school agrees to take.
     */
    public function addAction(ManageSupportPlan $manageSupportPlan): void
    {
        Gate::authorize('update', $this->plan);

        $this->validate([
            'actionDescription' => ['required', 'string', 'max:1000'],
            'actionDueOn' => ['nullable', 'date'],
            'actionAssigneeId' => ['nullable', 'integer', $this->memberOfWorkingSchool()],
        ]);

        try {
            $manageSupportPlan->addAction(
                plan: $this->plan,
                description: $this->actionDescription,
                dueOn: $this->actionDueOn === '' ? null : $this->actionDueOn,
                assignee: $this->actionAssigneeId === null ? null : User::findOrFail($this->actionAssigneeId),
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('actionDescription', $exception->getMessage());

            return;
        }

        $this->reset('actionDescription', 'actionAssigneeId', 'actionDueOn');
    }

    /**
     * Record that a step is done.
     */
    public function completeAction(int $actionId, ManageSupportPlan $manageSupportPlan): void
    {
        Gate::authorize('update', $this->plan);

        $manageSupportPlan->completeAction($this->plan->actions()->findOrFail($actionId), auth()->user());
    }

    /**
     * Write a note about how the plan is going.
     */
    public function addNote(ManageSupportPlan $manageSupportPlan): void
    {
        Gate::authorize('update', $this->plan);

        $this->validate(['noteBody' => ['required', 'string', 'max:5000']]);

        try {
            $manageSupportPlan->addNote(plan: $this->plan, body: $this->noteBody, actor: auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('noteBody', $exception->getMessage());

            return;
        }

        $this->reset('noteBody');
    }

    public function render(): View
    {
        $this->plan->load([
            'studentRecord.user:id,name',
            'actions.assignedTo:id,name',
            'notes.writtenBy:id,name',
            'statusChanges.changedBy:id,name',
            'createdBy:id,name',
            'assignedTo:id,name',
        ]);

        $canUpdate = auth()->user()->can('update', $this->plan);

        return view('livewire.show-support-plan', [
            'canUpdate' => $canUpdate,
            'nextStatuses' => $this->plan->status->allowedNext(),
            'staff' => $canUpdate ? $this->schoolStaff() : collect(),
        ]);
    }
}
