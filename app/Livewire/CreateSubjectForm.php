<?php

namespace App\Livewire;

use App\Http\Requests\SubjectStoreRequest;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Services\Subject\SubjectService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CreateSubjectForm extends Component
{
    public string $name = '';

    public string $short_name = '';

    #[Locked]
    public bool $setup = false;

    #[Locked]
    public ?int $academicYearId = null;

    public function mount(bool $setup = false, ?int $academicYearId = null): void
    {
        $this->setup = $setup;
        $this->academicYearId = $academicYearId;
    }

    public function render(): View
    {
        return view('livewire.create-subject-form');
    }

    public function save(SubjectService $subjects): void
    {
        Gate::authorize('create', Subject::class);

        $validated = $this->validate(SubjectStoreRequest::subjectRules());

        $subjects->createSubject($validated);

        session()->flash('success', $this->setup
            ? 'Subject created. Choose where it is taught.'
            : 'Subject created successfully');

        if ($this->setup) {
            $academicYear = AcademicYear::inSchool()->findOrFail($this->academicYearId);

            $this->redirectRoute('academic-years.setup', [$academicYear, 'subjects']);

            return;
        }

        $this->redirectRoute('subjects.index');
    }
}
