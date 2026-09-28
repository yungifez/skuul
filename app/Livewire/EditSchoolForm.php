<?php

namespace App\Livewire;

use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Livewire\Concerns\EditsSchoolDetails;
use App\Models\School;
use App\Services\School\SchoolService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Nnjeim\World\Models\Country;

/**
 * Change the details a campus is known by.
 *
 * During setup, saving moves on to the next setup step.
 */
class EditSchoolForm extends Component
{
    use DispatchesStatusNotifications;
    use EditsSchoolDetails;
    use WithFileUploads;

    #[Locked]
    public School $school;

    #[Locked]
    public bool $setup = false;

    public function mount(bool $setup = false): void
    {
        Gate::authorize('update', $this->school);

        $this->setup = $setup;
        $this->fillSchoolDetails($this->school);
    }

    public function save(SchoolService $schoolService): void
    {
        Gate::authorize('update', $this->school);

        $this->validate($this->schoolDetailRules(), [], $this->schoolDetailAttributes());

        $schoolService->updateSchool($this->school, $this->schoolDetails());

        if ($this->setup) {
            session()->flash('success', 'School details saved.');
            $this->redirectRoute('schools.setup', [$this->school, 'language']);

            return;
        }

        $this->logo = null;
        $this->school->refresh();
        $this->notify('The changes were saved.');
    }

    public function render(): View
    {
        return view('livewire.edit-school-form', [
            'countries' => Country::query()->orderBy('name')->get(['name']),
        ]);
    }
}
