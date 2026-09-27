<?php

namespace App\Livewire;

use App\Actions\Cohort\ChangeProgramParticipation;
use App\Actions\Cohort\SaveProgram;
use App\Enums\ParticipationStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Program;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * One programme: what it is, and the places learners hold in it.
 *
 * A place moves through its own states and never changes the learner's
 * enrollment.
 */
class ProgramRecord extends Component
{
    use DispatchesStatusNotifications;
    use ListsSchoolPeople;

    #[Locked]
    public Program $program;

    public bool $isEditing = false;

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    public string $studentRecordId = '';

    public string $startsOn = '';

    public string $schedule = '';

    public string $staffId = '';

    public function mount(Program $program): void
    {
        Gate::authorize('view', $program);

        $this->program = $program;
        $this->startsOn = now()->toDateString();
    }

    public function startEditing(): void
    {
        Gate::authorize('update', $this->program);

        $program = $this->program->fresh() ?? $this->program;
        $this->isEditing = true;
        $this->name = $program->name;
        $this->description = (string) $program->description;
        $this->isActive = $program->is_active;
    }

    public function stopEditing(): void
    {
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function save(SaveProgram $saveProgram): void
    {
        Gate::authorize('update', $this->program);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'isActive' => ['boolean'],
        ]);

        try {
            $this->program = $saveProgram->update($this->program, [
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'is_active' => $this->isActive,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        $this->isEditing = false;
        $this->notify('The programme was saved.');
    }

    public function givePlace(ChangeProgramParticipation $changeParticipation): void
    {
        Gate::authorize('update', $this->program);

        $this->schedule = trim($this->schedule);

        $this->validate([
            'studentRecordId' => ['required', 'integer', Rule::exists((new StudentRecord)->getTable(), 'id')->where('school_id', current_school_id())],
            'startsOn' => ['required', 'date'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'staffId' => ['nullable', 'integer', Rule::in($this->schoolStaff()->pluck('id')->all())],
        ], [
            'studentRecordId.exists' => 'Choose a learner of this school.',
            'staffId.in' => 'Choose somebody who works in this school.',
        ], ['studentRecordId' => 'learner', 'startsOn' => 'start date', 'staffId' => 'person running it']);

        try {
            $changeParticipation->join(
                program: $this->program,
                enrollment: StudentRecord::query()->inSchool()->findOrFail((int) $this->studentRecordId),
                startsOn: $this->startsOn,
                staff: $this->staffId === '' ? null : User::query()->findOrFail((int) $this->staffId),
                schedule: $this->schedule === '' ? null : $this->schedule,
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('studentRecordId', $exception->getMessage());

            return;
        }

        $this->reset('studentRecordId', 'schedule', 'staffId');
        $this->notify('The learner has a place.');
    }

    public function movePlace(int $participationId, string $status, string $seen, ChangeProgramParticipation $changeParticipation): void
    {
        Gate::authorize('update', $this->program);

        $place = $this->program->participations()->findOrFail($participationId);
        $status = ParticipationStatus::tryFrom($status);
        $seen = ParticipationStatus::tryFrom($seen);

        if ($status === null || $seen === null) {
            $this->notify('Choose a state from the list.', 'danger');

            return;
        }

        try {
            $place = $changeParticipation->changeStatus($place, $status, seen: $seen, actor: auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('The place is now '.$place->status->label().'.');
    }

    public function render(): View
    {
        $this->program->refresh()->load([
            'participations.studentRecord.user:id,name',
            'participations.staff:id,name',
        ]);

        $canWrite = Gate::allows('update', $this->program);

        return view('livewire.program-record', [
            'running' => $this->program->participations->filter(fn ($place): bool => $place->status->isRunning()),
            'canWrite' => $canWrite,
            'students' => $canWrite && $this->program->is_active ? $this->schoolLearners() : collect(),
            'staff' => $canWrite && $this->program->is_active ? $this->schoolStaff() : collect(),
        ]);
    }
}
