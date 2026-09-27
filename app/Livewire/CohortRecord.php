<?php

namespace App\Livewire;

use App\Actions\Cohort\ChangeCohortMembership;
use App\Actions\Cohort\SaveCohort;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Cohort;
use App\Models\StudentRecord;
use App\Traits\ListsSchoolPeople;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * One group: what it is for, who is in it now, and who held a place before.
 */
class CohortRecord extends Component
{
    use DispatchesStatusNotifications;
    use ListsSchoolPeople;

    #[Locked]
    public Cohort $cohort;

    public bool $isEditing = false;

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    public string $studentRecordId = '';

    public string $joinedOn = '';

    public function mount(Cohort $cohort): void
    {
        Gate::authorize('view', $cohort);

        $this->cohort = $cohort;
        $this->joinedOn = now()->toDateString();
    }

    public function startEditing(): void
    {
        Gate::authorize('update', $this->cohort);

        $cohort = $this->cohort->fresh() ?? $this->cohort;
        $this->isEditing = true;
        $this->name = $cohort->name;
        $this->description = (string) $cohort->description;
        $this->isActive = $cohort->is_active;
    }

    public function stopEditing(): void
    {
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function save(SaveCohort $saveCohort): void
    {
        Gate::authorize('update', $this->cohort);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'isActive' => ['boolean'],
        ]);

        try {
            $this->cohort = $saveCohort->update($this->cohort, [
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'is_active' => $this->isActive,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        $this->isEditing = false;
        $this->notify('The group was saved.');
    }

    public function addMember(ChangeCohortMembership $changeMembership): void
    {
        Gate::authorize('update', $this->cohort);

        $this->validate([
            'studentRecordId' => ['required', 'integer', Rule::exists((new StudentRecord)->getTable(), 'id')->where('school_id', current_school_id())],
            'joinedOn' => ['required', 'date'],
        ], [
            'studentRecordId.exists' => 'Choose a learner of this school.',
        ], ['studentRecordId' => 'learner', 'joinedOn' => 'joining date']);

        try {
            $changeMembership->addStudent(
                $this->cohort,
                StudentRecord::query()->inSchool()->findOrFail((int) $this->studentRecordId),
                $this->joinedOn,
                auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError(str_contains($exception->getMessage(), 'day') ? 'joinedOn' : 'studentRecordId', $exception->getMessage());

            return;
        }

        $this->reset('studentRecordId');
        $this->notify('The learner joined the group.');
    }

    public function removeMember(int $memberId, ChangeCohortMembership $changeMembership): void
    {
        Gate::authorize('update', $this->cohort);

        try {
            $changeMembership->remove($this->cohort->members()->findOrFail($memberId), actor: auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('The place was closed. The group still shows who held it.');
    }

    public function render(): View
    {
        $this->cohort->refresh()->load([
            'members.studentRecord.user:id,name',
            'members.user:id,name',
        ]);

        $canWrite = Gate::allows('update', $this->cohort);
        $current = $this->cohort->members->whereNull('left_on');
        $heldIds = $current->pluck('student_record_id')->filter()->all();

        return view('livewire.cohort-record', [
            'current' => $current,
            'past' => $this->cohort->members->whereNotNull('left_on'),
            'canWrite' => $canWrite,
            'students' => $canWrite && $this->cohort->is_active
                ? $this->schoolLearners()->reject(fn (StudentRecord $student): bool => in_array($student->id, $heldIds, true))
                : collect(),
        ]);
    }
}
