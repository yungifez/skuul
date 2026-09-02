<?php

namespace App\Livewire;

use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AttendanceRecord;
use App\Models\CalendarEvent;
use App\Models\CourseOffering;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Services\School\SchoolSetupPhaseService;
use Illuminate\View\View;
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

    public ?array $setupChecklist = null;

    /** @var array{registered: int, present: int, absent: int, late: int, rate: float|null} */
    public array $todayAttendance = [
        'registered' => 0,
        'present' => 0,
        'absent' => 0,
        'late' => 0,
        'rate' => null,
    ];

    /** @var array<int, array{label: string, date: string, rate: float|null, registered: int}> */
    public array $attendanceTrend = [];

    /** @var array<int, array{title: string, type: string, time: string, location: string|null}> */
    public array $todayEvents = [];

    /** @var array<int, array{title: string, type: string, date: string, time: string}> */
    public array $upcomingEvents = [];

    public function mount(SchoolSetupPhaseService $schoolSetupPhases): void
    {
        $user = auth()->user();
        $school = current_school();
        $currentAcademicYear = current_academic_year();

        if ($user->can('manage school settings')) {
            $setupState = $schoolSetupPhases->for($school);
            $this->setupChecklist = $setupState['show_dashboard_card'] ? $setupState : null;
        }
        $this->organization = $school->organization;
        $this->organizationSchools = $this->organization?->schools()->count() ?? 0;
        $this->calendarTemplates = $this->organization?->calendarTemplates()->count() ?? 0;
        $this->organizations = $user->can(PlatformPermission::AccessAllOrganizations) ? Organization::count() : 0;
        $this->isOrganizationAdministrator = $this->organization !== null
            && $user->administersOrganization($this->organization);
        $this->schools = School::count();
        $this->academicLevels = AcademicLevel::query()->inSchool()->where('is_group', false)->count();
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

        $this->loadAttendanceOverview($currentAcademicYear?->id);

        if ($user->can('viewAny', CalendarEvent::class)) {
            $this->loadCalendarOverview();
        }
    }

    public function render(): View
    {
        return view('livewire.dashboard-data-cards');
    }

    private function loadAttendanceOverview(?int $academicYearId): void
    {
        if (!auth()->user()->can('read attendance')) {
            return;
        }

        $today = now();
        $weekStart = $today->copy()->subDays(6);

        $records = AttendanceRecord::query()
            ->inSchool()
            ->ofKind(AttendanceKind::Daily)
            ->when($academicYearId !== null, fn ($query) => $query->where('academic_year_id', $academicYearId))
            ->whereBetween('attended_on', [$weekStart->toDateString(), $today->toDateString()])
            ->get(['attended_on', 'status']);

        $todayRecords = $records->filter(fn (AttendanceRecord $record): bool => $record->attended_on->isSameDay($today));
        $registered = $todayRecords->filter(fn (AttendanceRecord $record): bool => $record->status !== AttendanceStatus::NotRecorded);
        $present = $registered->filter(fn (AttendanceRecord $record): bool => $record->status->countsAsPresent())->count();
        $absent = $registered->where('status', AttendanceStatus::Absent)->count();
        $late = $registered->where('status', AttendanceStatus::Late)->count();

        $this->todayAttendance = [
            'registered' => $registered->count(),
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'rate' => $registered->isEmpty() ? null : round(($present / $registered->count()) * 100, 1),
        ];

        $this->attendanceTrend = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($today, $records): array {
                $date = $today->copy()->subDays($daysAgo);
                $dayRecords = $records->filter(fn (AttendanceRecord $record): bool => $record->attended_on->isSameDay($date))
                    ->filter(fn (AttendanceRecord $record): bool => $record->status !== AttendanceStatus::NotRecorded);
                $present = $dayRecords->filter(fn (AttendanceRecord $record): bool => $record->status->countsAsPresent())->count();

                return [
                    'label' => $date->format('D'),
                    'date' => $date->format('M j'),
                    'rate' => $dayRecords->isEmpty() ? null : round(($present / $dayRecords->count()) * 100, 1),
                    'registered' => $dayRecords->count(),
                ];
            })
            ->all();
    }

    private function loadCalendarOverview(): void
    {
        $today = now();

        $this->todayEvents = CalendarEvent::query()
            ->inSchool()
            ->published()
            ->covering($today)
            ->orderByDesc('is_all_day')
            ->orderBy('starts_at')
            ->limit(5)
            ->get(['title', 'type', 'is_all_day', 'starts_at', 'location'])
            ->map(fn (CalendarEvent $event): array => [
                'title' => $event->title,
                'type' => $event->type->label(),
                'time' => $event->is_all_day ? 'All day' : $event->starts_at->format('g:i A'),
                'location' => $event->location,
            ])
            ->all();

        $this->upcomingEvents = CalendarEvent::query()
            ->inSchool()
            ->published()
            ->between($today->copy()->addDay(), $today->copy()->addDays(7))
            ->orderBy('starts_at')
            ->limit(5)
            ->get(['title', 'type', 'is_all_day', 'starts_at'])
            ->map(fn (CalendarEvent $event): array => [
                'title' => $event->title,
                'type' => $event->type->label(),
                'date' => $event->starts_at->format('D, M j'),
                'time' => $event->is_all_day ? 'All day' : $event->starts_at->format('g:i A'),
            ])
            ->all();
    }
}
