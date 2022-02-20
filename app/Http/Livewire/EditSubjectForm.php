<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;
use App\Services\Teacher\TeacherService;

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
