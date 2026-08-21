<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Models\School;
use App\Models\User;
use App\Services\Section\SectionService;
use Livewire\Component;

class DashboardDataCards extends Component
{
    public $schools;

    public $classes;

    public $sections;

    public $students;

    public $classGroups;

    public $teachers;

    public $parents;

    public function mount(SectionService $sectionService)
    {
        $this->schools = School::count();
        $this->classGroups = current_school()->classGroups()->count();
        $this->classes = current_school()->myClasses()->count();
        $this->sections = $sectionService->getAllSections()->count();
        $this->students = User::ofSchool()->students()->activeStudents()->count();
        $this->teachers = User::ofSchool()->role(Role::Teacher)->count();
        $this->parents = User::ofSchool()->role(Role::Parent)->count();
    }

    public function render()
    {
        return view('livewire.dashboard-data-cards');
    }
}
