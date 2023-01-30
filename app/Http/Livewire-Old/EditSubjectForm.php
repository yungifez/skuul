<?php

namespace App\Http\Livewire;

use App\Services\Teacher\TeacherService;
use Livewire\Component;

class EditSubjectForm extends Component
{
    public object $subject;

    public function mount(TeacherService $teacherService)
    {
        $this->teachers = $teacherService->getAllTeachers();
        $this->assignedTeachersId = $this->subject->teachers()->get()->pluck('id')->toArray();
    }

    public function render()
    {
        return view('livewire.edit-subject-form');
    }
}
