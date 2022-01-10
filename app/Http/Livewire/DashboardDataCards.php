<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\School\SchoolService;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
use App\Services\Student\StudentService;
use App\Services\Teacher\TeacherService;

class DashboardDataCards extends Component
{
    public function mount(SchoolService $schoolService,MyClassService $myClassService,SectionService $sectionService,StudentService $studentService,TeacherService $teacherService)
    { 
        $this->schools = $schoolService->getAllSchools()->count();
        $this->classGroups = $myClassService->getAllClassGroups()->count();
        $this->classes = $myClassService->getAllClasses()->count();
        $this->sections = $sectionService->getAllSections()->count();
        $this->students = $studentService->getAllStudents()->count();
        $this->teachers = $teacherService->getAllTeachers()->count();
    }
    
    public function render()
    {
        return view('livewire.dashboard-data-cards');
    }
}
