<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\School;
use Livewire\Component;
use App\Services\Section\SectionService;

class DashboardDataCards extends Component
{
    public $schools, $classes, $sections, $students, $classGroups, $teachers, $parents ;
    
    public function mount(SectionService $sectionService)
    {
        $this->schools = School::count();
        $this->classGroups = auth()->user()->school->classGroups()->count();
        $this->classes =  auth()->user()->school->myClasses()->count();
        $this->sections = $sectionService->getAllSections()->count();
        $this->students = User::inSchool()->students()->activeStudents()->count();
        $this->teachers = User::inSchool()->role('teacher')->count();
        $this->parents =  User::inSchool()->role('parent')->count();
    }

    public function render()
    {
        return view('livewire.dashboard-data-cards');
    }
}
