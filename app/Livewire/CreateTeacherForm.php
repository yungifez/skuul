<?php

namespace App\Livewire;

use App\Livewire\Concerns\EditsPersonDetails;
use App\Models\User;
use App\Services\Teacher\TeacherService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Add a teacher to the school and invite them to set a password.
 */
class CreateTeacherForm extends Component
{
    use EditsPersonDetails;
    use WithFileUploads;

    public function mount(): void
    {
        Gate::authorize('create', [User::class, 'teacher']);
    }

    public function save(TeacherService $teacherService): void
    {
        Gate::authorize('create', [User::class, 'teacher']);

        $this->trimPersonDetails();
        $this->validate($this->personDetailRules(), [], $this->personDetailAttributes());

        try {
            $teacher = $teacherService->createTeacher($this->personDetails());
        } catch (ValidationException $exception) {
            $this->showPersonDetailErrors($exception);

            return;
        }

        session()->flash('success', $teacher->isAwaitingInvitationAcceptance()
            ? "{$teacher->name} was added. We emailed them a link to set a password."
            : "{$teacher->name} was added and can sign in with their existing password.");

        if (Gate::allows('view', [$teacher, 'teacher'])) {
            $this->redirectRoute('teachers.show', $teacher);

            return;
        }

        $this->redirectRoute('teachers.create');
    }

    public function render(): View
    {
        return view('livewire.create-teacher-form');
    }
}
