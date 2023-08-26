<?php

namespace App\Livewire;

use App\Services\MyClass\MyClassService;
use App\Services\Teacher\TeacherService;
use Livewire\Component;

class CreateSubjectForm extends Component
{
    public $classes;

    public $teachers;

    public function mount(MyClassService $myClassService, TeacherService $teacherService)
    {
        $this->classes = $myClassService->getAllClasses();
        $this->teachers = $teacherService->getAllTeachers();
    }

    public function render()
    {
        return view('livewire.create-subject-form');
    }
}
