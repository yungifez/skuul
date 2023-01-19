<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use App\Services\Parent\ParentService;
use App\Services\School\SchoolService;
use App\Services\Section\SectionService;
use App\Services\Student\StudentService;
use App\Services\Teacher\TeacherService;
use Livewire\Component;

class DashboardDataCards extends Component
{
    public $schools, $classes, $sections, $students, $classGroups, $teachers, $parents ;
    
    public function mount(SchoolService $schoolService, MyClassService $myClassService, SectionService $sectionService, StudentService $studentService, TeacherService $teacherService, ParentService $parentService)
    {
        $this->schools = $schoolService->getAllSchools()->count();
        $this->classGroups = $myClassService->getAllClassGroups()->count();
        $this->classes = $myClassService->getAllClasses()->count();
        $this->sections = $sectionService->getAllSections()->count();
        $this->students = $studentService->getAllActiveStudents()->count();
        $this->teachers = $teacherService->getAllTeachers()->count();
        $this->parents = $parentService->getAllParents()->count();
    }

    public function render()
    {
        return view('livewire.dashboard-data-cards');
    }
}
