<?php

namespace App\Livewire;

use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\CourseOffering;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use Livewire\Component;

class DashboardDataCards extends Component
{
    public $schools;

    public $academicLevels;

    public $cycleSections;

    public $academicPeriods;

    public $courseOfferings;

    public $students;

    public $teachers;

    public $parents;

    public $organizations;

    public $organization;

    public $organizationSchools;

    public $calendarTemplates;

    public $isOrganizationAdministrator;

    public function mount(): void
    {
        $user = auth()->user();
        $currentAcademicYear = current_academic_year();

        $this->organization = current_school()->organization;
        $this->organizationSchools = $this->organization?->schools()->count() ?? 0;
        $this->calendarTemplates = $this->organization?->calendarTemplates()->count() ?? 0;
        $this->organizations = $user->can(PlatformPermission::AccessAllOrganizations) ? Organization::count() : 0;
        $this->isOrganizationAdministrator = $this->organization !== null
            && $user->administersOrganization($this->organization);
        $this->schools = School::count();
        $this->academicLevels = AcademicLevel::query()->inSchool()->count();
        $this->cycleSections = AcademicCycleSection::query()
            ->inSchool()
            ->when($currentAcademicYear !== null, fn ($query) => $query->where('academic_year_id', $currentAcademicYear->id))
            ->count();
        $this->academicPeriods = $currentAcademicYear?->academicPeriods()->count() ?? 0;
        $this->courseOfferings = CourseOffering::query()
            ->inSchool()
            ->when($currentAcademicYear !== null, fn ($query) => $query->where('academic_year_id', $currentAcademicYear->id))
            ->count();
        $this->students = User::ofSchool()->students()->activeStudents()->count();
        $this->teachers = User::ofSchool()->role(Role::Teacher)->count();
        $this->parents = User::ofSchool()->role(Role::Parent)->count();
    }

    public function render()
    {
        return view('livewire.dashboard-data-cards');
    }
}
