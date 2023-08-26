<?php

namespace App\Livewire;

use App\Models\MyClass;
use App\Models\User;
use App\Services\MyClass\MyClassService;
use App\Services\Teacher\TeacherService;
use Livewire\Component;

class AssignTeacherToSubjects extends Component
{
    public $teachers;

    public $classes;

    public ?int $class;

    public $subjects;

    public $teacher;

    /**
     * State variable for teacher.
     */
    public User $teacherState;

    public function mount(TeacherService $teacherService, MyClassService $myclassService)
    {
        $this->classes = $myclassService->getAllClasses();
        $this->class = $this->classes->first()?->id;
        $this->teachers = $teacherService->getAllTeachers();
        $this->teacher = $this->teachers->first()?->id;
    }

    public function fetchSubjects(MyClass $class, User $teacher)
    {
        $this->subjects = $class->subjects;
        $this->teacherState = $teacher;
    }

    public function render()
    {
        return view('livewire.assign-teacher-to-subjects');
    }
}
