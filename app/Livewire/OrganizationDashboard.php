<?php

namespace App\Livewire;

use App\Enums\AcademicPeriodStatus;
use App\Models\Organization;
use App\Models\School;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class OrganizationDashboard extends Component
{
    public Organization $organization;

    /** @var Collection<int, School> */
    public Collection $campuses;

    public int $campusCount = 0;

    public int $activeStudents = 0;

    public int $campusAccessHolders = 0;

    public int $campusesMissingAcademicSetup = 0;

    public bool $loaded = false;

    public function mount(Organization $organization): void
    {
        $this->organization = $organization;
        $this->campuses = new Collection();

        $this->authorizeOrganization();
    }

    /**
     * Load aggregate campus health data without exposing school operational records.
     */
    public function loadDashboard(): void
    {
        $this->authorizeOrganization();

        $studentRecords = new StudentRecord();
        $schools = new School();

        $this->campuses = $this->organization->schools()
            ->select([
                'id',
                'organization_id',
                'name',
                'academic_year_id',
                'academic_period_id',
            ])
            ->with([
                'academicYear:id,school_id,start_year,stop_year,status',
                'academicPeriod:id,school_id,academic_year_id,name,status',
            ])
            ->withCount([
                'memberships as campus_access_count' => fn ($query) => $query->active(),
            ])
            ->addSelect([
                'active_students_count' => StudentRecord::query()
                    ->selectRaw('count(*)')
                    ->attending()
                    ->whereColumn(
                        $studentRecords->qualifyColumn('school_id'),
                        $schools->qualifyColumn('id'),
                    ),
            ])
            ->orderBy('name')
            ->get();

        $this->campusCount = $this->campuses->count();
        $this->activeStudents = (int) $this->campuses->sum('active_students_count');
        $this->campusAccessHolders = (int) $this->campuses->sum('campus_access_count');
        $this->campusesMissingAcademicSetup = $this->campuses
            ->filter(fn (School $campus): bool => !$this->hasRequiredAcademicSetup($campus))
            ->count();
        $this->loaded = true;
    }

    /**
     * Check that the selected year and academic period form one usable academic setup.
     */
    public function hasRequiredAcademicSetup(School $campus): bool
    {
        return $campus->academicYear !== null
            && $campus->academicPeriod !== null
            && $campus->academicPeriod->academic_year_id === $campus->academicYear->id;
    }

    /**
     * Return the display status for a selected academic period.
     */
    public function academicPeriodStatus(?AcademicPeriodStatus $status): string
    {
        return $status?->label() ?? 'Not set';
    }

    public function render(): View
    {
        return view('livewire.organization-dashboard');
    }

    private function authorizeOrganization(): void
    {
        Gate::authorize('viewReports', $this->organization);
    }
}
