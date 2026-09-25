<?php

namespace App\Livewire;

use App\Http\Requests\SubjectStoreRequest;
use App\Models\Subject;
use App\Services\Subject\SubjectService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EditSubjectForm extends Component
{
    #[Locked]
    public Subject $subject;

    public string $name = '';

    public string $short_name = '';

    public function mount(Subject $subject): void
    {
        Gate::authorize('update', $subject);

        $this->subject = $subject;
        $this->name = $subject->name;
        $this->short_name = $subject->short_name;
    }

    public function render(): View
    {
        return view('livewire.edit-subject-form');
    }

    public function save(SubjectService $subjects): void
    {
        Gate::authorize('update', $this->subject);

        $validated = $this->validate(SubjectStoreRequest::subjectRules());

        $subjects->updateSubject($this->subject, $validated);

        session()->flash('success', 'Subject updated successfully');

        $this->redirectRoute('subjects.index');
    }
}
