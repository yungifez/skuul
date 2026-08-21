<?php

namespace App\Livewire;

use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
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

    public $organizations;

    public $organization;

    public $organizationSchools;

    public $isOrganizationAdministrator;

    public function mount(): void
    {
        $user = auth()->user();
        $this->organization = current_school()->organization;
        $this->organizationSchools = $this->organization?->schools()->count() ?? 0;
        $this->organizations = $user->can(PlatformPermission::AccessAllOrganizations) ? Organization::count() : 0;
        $this->isOrganizationAdministrator = $this->organization !== null
            && $user->administersOrganization($this->organization);
        $this->schools = School::count();
        $this->classGroups = current_school()->classGroups()->count();
        $classCounts = current_school()->myClasses()->withCount('sections')->get();
        $this->classes = $classCounts->count();
        $this->sections = $classCounts->sum('sections_count');
        $this->students = User::ofSchool()->students()->activeStudents()->count();
        $this->teachers = User::ofSchool()->role(Role::Teacher)->count();
        $this->parents = User::ofSchool()->role(Role::Parent)->count();
    }

    public function render()
    {
        return view('livewire.dashboard-data-cards');
    }
}
