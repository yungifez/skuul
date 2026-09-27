<?php

namespace App\Livewire;

use App\Actions\Discipline\AddIncidentNote;
use App\Actions\Discipline\ReportIncident;
use App\Enums\IncidentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Incident;
use App\Models\IncidentNote;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Show one case and let its handlers move it, add actions, and write notes.
 *
 * Every change is appended to the case. Nothing on this screen edits what
 * somebody wrote before.
 */
class ShowIncident extends Component
{
    use ListsSchoolPeople;

    public Incident $incident;

    public string $nextStatus = '';

    public string $statusReason = '';

    public string $actionType = '';

    public string $actionDescription = '';

    public ?int $actionAssigneeId = null;

    public string $actionDueOn = '';

    public string $noteBody = '';

    public bool $noteIsRestricted = true;

    public function mount(Incident $incident): void
    {
        Gate::authorize('view', $incident);

        $this->incident = $incident;
        $this->nextStatus = $incident->status->allowedNext()[0]->value ?? '';
    }

    /**
     * Move the case to another state.
     */
    public function changeStatus(ReportIncident $reportIncident): void
    {
        Gate::authorize('update', $this->incident);

        $this->validate([
            'nextStatus' => ['required', Rule::enum(IncidentStatus::class)],
            'statusReason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $reportIncident->changeStatus(
                incident: $this->incident,
                status: IncidentStatus::from($this->nextStatus),
                actor: auth()->user(),
                reason: $this->statusReason === '' ? null : $this->statusReason,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('nextStatus', $exception->getMessage());

            return;
        }

        $this->incident->refresh();
        $this->reset('statusReason');
        $this->nextStatus = $this->incident->status->allowedNext()[0]->value ?? '';
    }

    /**
     * Record something the school will do about the case.
     */
    public function addAction(ReportIncident $reportIncident): void
    {
        Gate::authorize('update', $this->incident);

        $this->validate([
            'actionType' => ['required', 'string', 'max:100'],
            'actionDescription' => ['required', 'string', 'max:1000'],
            'actionDueOn' => ['nullable', 'date'],
            'actionAssigneeId' => ['nullable', 'integer', Rule::exists('school_memberships', 'user_id')->where('school_id', current_school_id())],
        ]);

        try {
            $reportIncident->addAction(
                incident: $this->incident,
                type: $this->actionType,
                description: $this->actionDescription,
                dueOn: $this->actionDueOn === '' ? null : $this->actionDueOn,
                assignee: $this->actionAssigneeId === null ? null : User::findOrFail($this->actionAssigneeId),
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('actionType', $exception->getMessage());

            return;
        }

        $this->reset('actionType', 'actionDescription', 'actionAssigneeId', 'actionDueOn');
    }

    /**
     * Record that an action is done.
     */
    public function completeAction(int $actionId): void
    {
        Gate::authorize('update', $this->incident);

        $this->incident->actions()->findOrFail($actionId)->complete();
    }

    /**
     * Add one append-only note to an open case.
     */
    public function addNote(AddIncidentNote $addIncidentNote): void
    {
        Gate::authorize('update', $this->incident);

        $this->validate([
            'noteBody' => ['required', 'string', 'max:5000'],
            'noteIsRestricted' => ['boolean'],
        ]);

        try {
            $addIncidentNote->add(
                incident: $this->incident,
                body: $this->noteBody,
                restricted: $this->noteIsRestricted,
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('noteBody', $exception->getMessage());

            return;
        }

        $this->reset('noteBody', 'noteIsRestricted');
    }

    public function render(): View
    {
        $this->incident->load([
            'participants.user:id,name',
            'participants.studentRecord.user:id,name',
            'actions.assignedTo:id,name',
            'statusChanges.changedBy:id,name',
            'reportedBy:id,name',
            'assignedTo:id,name',
            'notes.writtenBy:id,name',
        ]);

        $canUpdate = auth()->user()->can('update', $this->incident);

        return view('livewire.show-incident', [
            'canUpdate' => $canUpdate,
            'nextStatuses' => $this->incident->status->allowedNext(),
            'staff' => $canUpdate ? $this->schoolStaff() : collect(),
            'notes' => $this->incident->notes
                ->filter(fn (IncidentNote $note): bool => auth()->user()->can('view', $note))
                ->values(),
        ]);
    }
}
