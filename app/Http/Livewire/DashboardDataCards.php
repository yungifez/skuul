<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\School\SchoolService;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;

class DashboardDataCards extends Component
{
    public $schools;
    public $classGroups;
    public $myClasses;

    public function mount(SchoolService $schoolService,MyClassService $myClassService,SectionService $sectionService)
    { 
        $this->schools = $schoolService->getAllSchools()->count();
        $this->classGroups = $myClassService->getAllClassGroups()->count();
        $this->classes = $myClassService->getAllClasses()->count();
        $this->sections = $sectionService->getAllSections()->count();
    }
    
    public function render()
    {
        return view('livewire.dashboard-data-cards');
    }
}
