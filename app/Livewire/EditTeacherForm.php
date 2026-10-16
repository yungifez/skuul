<?php

namespace App\Livewire;

use App\Livewire\Concerns\EditsPersonDetails;
use App\Models\User;
use App\Services\Teacher\TeacherService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Change the identity and contact details of a teacher.
 */
class EditTeacherForm extends Component
{
    use EditsPersonDetails;
    use WithFileUploads;

    #[Locked]
    public User $teacher;

    public function mount(): void
    {
        Gate::authorize('update', [$this->teacher, 'teacher']);

        $this->fillPersonDetails($this->teacher);
    }

    public function save(TeacherService $teacherService): void
    {
        Gate::authorize('update', [$this->teacher, 'teacher']);

        $this->trimPersonDetails();
        $rules = $this->personDetailRules();
        $rules['email'][] = Rule::unique('users', 'email')->ignore($this->teacher->id);
        $this->validate($rules, [], $this->personDetailAttributes());

        try {
            $teacherService->updateTeacher($this->teacher, $this->personDetails());
        } catch (ValidationException $exception) {
            $this->showPersonDetailErrors($exception);

            return;
        }

        session()->flash('success', 'The changes were saved.');
        $this->redirectRoute('teachers.show', $this->teacher);
    }

    public function render(): View
    {
        return view('livewire.edit-teacher-form');
    }
}
