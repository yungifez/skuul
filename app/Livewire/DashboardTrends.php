<?php

namespace App\Livewire;

use App\Services\Dashboard\SchoolTrends;
use Illuminate\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;

/**
 * Draw the school's recent trends under the dashboard's daily view.
 *
 * The charts load right after the page, so a slow count never holds up the
 * register button or today's calendar. Each chart needs its own permission
 * and stays hidden until it has something to draw.
 */
#[Defer]
class DashboardTrends extends Component
{
    /** @var array{weeks: list<array{week: string, rate: float|null}>, recent: float|null, previous: float|null}|null */
    public ?array $attendance = null;

    /** @var list<array{month: string, billed: float, collected: float}>|null */
    public ?array $fees = null;

    /** @var list<array{label: string, owed: float}>|null */
    public ?array $owed = null;

    /** @var list<array{month: string, received: float, spent: float}>|null */
    public ?array $cash = null;

    /** @var list<array{week: string, incidents: int}>|null */
    public ?array $incidents = null;

    /** @var list<array{name: string, students: int, capacity: int}>|null */
    public ?array $enrolment = null;

    public function mount(SchoolTrends $trends): void
    {
        $user = auth()->user();
        $today = school_today()->toImmutable();

        // Families hold read permissions for their own learners. The school's
        // figures are for staff.
        if ($user->isPortalOnly()) {
            return;
        }

        if ($user->can('read attendance')) {
            $attendance = $trends->attendanceByWeek($today);
            $this->attendance = $attendance['recent'] === null && $attendance['previous'] === null ? null : $attendance;
        }

        if ($user->can('read fee invoice')) {
            $fees = $trends->feesByMonth($today);
            $this->fees = collect($fees)->every(fn (array $month): bool => $month['billed'] == 0 && $month['collected'] == 0) ? null : $fees;

            $owed = $trends->owedByLateness($today);
            $this->owed = collect($owed)->sum('owed') == 0 ? null : $owed;
        }

        if ($user->can('read fee invoice') && $user->can('read expense')) {
            $cash = $trends->cashByMonth($today);
            $this->cash = collect($cash)->every(fn (array $month): bool => $month['received'] == 0 && $month['spent'] == 0) ? null : $cash;
        }

        if ($user->can('read incident')) {
            $incidents = $trends->incidentsByWeek($user, $today);
            $this->incidents = collect($incidents)->sum('incidents') === 0 ? null : $incidents;
        }

        $academicYear = current_academic_year();

        if ($user->can('read student') && $academicYear !== null) {
            $enrolment = $trends->enrolmentByClass($academicYear->id);
            $this->enrolment = collect($enrolment)->sum('students') === 0 ? null : $enrolment;
        }
    }

    public function hasTrends(): bool
    {
        return $this->attendance !== null || $this->fees !== null || $this->owed !== null || $this->cash !== null || $this->incidents !== null || $this->enrolment !== null;
    }

    public function placeholder(): View
    {
        return view('livewire.placeholders.dashboard-trends');
    }

    public function render(): View
    {
        return view('livewire.dashboard-trends');
    }
}
