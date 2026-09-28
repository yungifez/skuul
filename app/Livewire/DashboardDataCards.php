<?php

namespace App\Livewire;

use App\Enums\AcademicPeriodStatus;
use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AttendanceRecord;
use App\Models\CalendarEvent;
use App\Models\CourseOffering;
use App\Models\Notice;
use App\Models\User;
use App\Services\School\SchoolSetupPhaseService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class DashboardDataCards extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public $academicLevels;

    #[Locked]
    public $cycleSections;

    #[Locked]
    public $academicPeriods;

    #[Locked]
    public $courseOfferings;

    #[Locked]
    public $students;

    #[Locked]
    public $teachers;

    #[Locked]
    public $parents;

    #[Locked]
    public $organization;

    #[Locked]
    public $organizationSchools;

    #[Locked]
    public bool $showCampuses = false;

    #[Locked]
    public bool $canOpenOrganization = false;

    #[Locked]
    public ?array $setupChecklist = null;

    /** @var array{registered: int, present: int, absent: int, late: int, rate: float|null} */
    #[Locked]
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

    /** @var array<int, array{title: string, until: string, url: string}>|null */
    public ?array $notices = null;

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
        $this->showCampuses = $this->organization !== null && (
            $user->can(PlatformPermission::AccessAllSchools)
            || $user->administersOrganization($this->organization)
            || $user->hasRole(Role::Admin)
        );
        $this->canOpenOrganization = $this->showCampuses && $user->can('view', $this->organization);
        $this->academicLevels = AcademicLevel::query()->inSchool()->where('is_group', false)->count();
        $this->cycleSections = AcademicCycleSection::query()
            ->inSchool()
            ->when($currentAcademicYear !== null, fn ($query) => $query->where('academic_year_id', $currentAcademicYear->id))
            ->count();
        $this->academicPeriods = $currentAcademicYear?->academicPeriods()->where('status', AcademicPeriodStatus::Open)->count() ?? 0;
        $this->courseOfferings = CourseOffering::query()
            ->inSchool()
            ->when($currentAcademicYear !== null, fn ($query) => $query->where('academic_year_id', $currentAcademicYear->id))
            ->count();
        $this->students = User::ofSchool()->students()->enrolledStudents()->count();
        $this->teachers = User::ofSchool()->role(Role::Teacher)->count();
        $this->parents = User::ofSchool()->role(Role::Parent)->count();

        $this->loadAttendanceOverview($currentAcademicYear?->id);

        if ($user->can('viewAny', CalendarEvent::class)) {
            $this->loadCalendarOverview();
        }

        if ($user->can('read notice')) {
            $this->loadCurrentNotices($user);
        }
    }

    /**
     * Say the school is ready for daily work. A tab left open while setup slipped back is told so instead.
     */
    public function acknowledgeSetup(SchoolSetupPhaseService $schoolSetupPhases): void
    {
        $school = current_school();
        Gate::authorize('update', $school);

        /** @var User $actor */
        $actor = auth()->user();

        if (!$schoolSetupPhases->acknowledge($school, $actor)) {
            $setupState = $schoolSetupPhases->for($school);
            $this->setupChecklist = $setupState['show_dashboard_card'] ? $setupState : null;
            $this->notify('Setup needs attention again. Finish the steps it lists first.', 'danger');

            return;
        }

        session()->flash('success', 'Your school is ready for daily work.');
        $this->redirectRoute('dashboard');
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

    /**
     * Load the notices running today that this person may read.
     */
    private function loadCurrentNotices(User $user): void
    {
        $this->notices = Notice::query()
            ->inSchool()
            ->published()
            ->active()
            ->when(
                $user->isPortalOnly(),
                fn ($query) => $query->whereHas('recipients', fn ($recipients) => $recipients->where('user_id', $user->id)),
            )
            ->orderBy('stop_date')
            ->limit(5)
            ->get(['id', 'title', 'stop_date'])
            ->map(fn (Notice $notice): array => [
                'title' => $notice->title,
                'until' => Carbon::parse($notice->stop_date)->format('M j'),
                'url' => route('notices.show', $notice),
            ])
            ->all();
    }
}
