<?php

namespace App\Livewire;

use App\Models\Timetable;
use App\Services\Timetable\TimetableService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Rename a draft timetable and change its description.
 */
class EditTimetableForm extends Component
{
    #[Locked]
    public Timetable $timetable;

    public string $name = '';

    public string $description = '';

    public function mount(): void
    {
        Gate::authorize('update', $this->timetable);

        $this->timetable->loadMissing('academicCycleSection.academicLevel');
        $this->name = (string) $this->timetable->name;
        $this->description = (string) $this->timetable->description;
    }

    public function save(TimetableService $timetableService): void
    {
        $this->timetable->refresh();

        // Someone may have published the timetable while this page was open.
        if (!$this->timetable->acceptsChanges()) {
            $this->addError('name', 'This timetable was published, so it can no longer be edited.');

            return;
        }

        Gate::authorize('update', $this->timetable);

        $this->name = trim($this->name);
        $this->description = trim($this->description);
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);

        $timetableService->updateTimetable($this->timetable, [
            'name' => $this->name,
            'description' => $this->description === '' ? null : $this->description,
        ]);

        session()->flash('success', 'The timetable was saved.');
        $this->redirectRoute('timetables.show', $this->timetable);
    }

    public function render(): View
    {
        return view('livewire.edit-timetable-form');
    }
}
