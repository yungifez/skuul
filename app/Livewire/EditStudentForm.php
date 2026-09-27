<?php

namespace App\Livewire;

use App\Livewire\Concerns\EditsPersonDetails;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Change the identity and contact details of an studentistrator.
 */
class EditStudentForm extends Component
{
    use EditsPersonDetails;
    use WithFileUploads;

    #[Locked]
    public User $student;

    public function mount(): void
    {
        Gate::authorize('update', [$this->student, 'student']);

        $this->fillPersonDetails($this->student);
    }

    public function save(StudentService $studentService): void
    {
        Gate::authorize('update', [$this->student, 'student']);

        $this->trimPersonDetails();
        $rules = $this->personDetailRules();
        $rules['email'][] = Rule::unique('users', 'email')->ignore($this->student->id);
        $this->validate($rules, [], $this->personDetailAttributes());

        try {
            $studentService->updateStudent($this->student, $this->personDetails());
        } catch (ValidationException $exception) {
            $this->showPersonDetailErrors($exception);

            return;
        }

        session()->flash('success', 'The changes were saved.');
        $this->redirectRoute('students.show', $this->student);
    }

    public function render(): View
    {
        return view('livewire.edit-student-form');
    }
}
