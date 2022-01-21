<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;
use App\Services\Teacher\TeacherService;

class CreateSubjectForm extends Component
{
    public function mount(MyClassService $myClassService,TeacherService $teacherService)
    {
        $this->classes = $myClassService->getAllClasses();
        $this->teachers = $teacherService->getAllTeachers();
    }

    public function render()
    {
        return view('livewire.create-subject-form');
    }
}
