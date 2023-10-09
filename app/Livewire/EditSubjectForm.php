<?php

namespace App\Livewire;

use App\Services\Teacher\TeacherService;
use Livewire\Component;

class EditSubjectForm extends Component
{
    public object $subject;

    public $teachers;

    public $assignedTeachersId;

    public function mount(TeacherService $teacherService)
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());

        $this->teachers = $teacherService->getAllTeachers();
        $this->assignedTeachersId = $this->subject->teachers()->get()->pluck('id')->all();
    }

    public function render()
    {
        return view('livewire.edit-subject-form');
    }
}
