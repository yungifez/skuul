<?php

namespace App\Livewire;

use App\Http\Requests\SchoolSetRequest;
use App\Models\School;
use App\Services\School\SchoolService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class SetSchool extends Component
{
    public ?int $school_id = null;

    public function mount(): void
    {
        Gate::authorize('setSchool', School::class);

        $this->school_id = current_school_id();
    }

    public function render(SchoolService $schoolService): View
    {
        return view('livewire.set-school', [
            'schools' => $schoolService->getSchoolsForUser(),
        ]);
    }

    public function setSchool(SchoolService $schoolService): void
    {
        Gate::authorize('setSchool', School::class);

        $validated = $this->validate(SchoolSetRequest::schoolRules());
        $school = School::query()->findOrFail($validated['school_id']);

        $schoolService->setSchool($school);

        session()->flash('success', __('School set successfully'));

        $this->redirectRoute('dashboard');
    }
}
