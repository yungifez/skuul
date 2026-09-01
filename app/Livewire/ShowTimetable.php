<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Models\AcademicPeriod;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\Weekday;
use App\Services\Timetable\TimetableCalendar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Read one timetable in a calendar view.
 *
 * The calendar keeps the same audience filtering and term-scoped recurrence
 * rules as the weekly timetable grid, but lets people scan a month, week, or
 * day without opening the builder.
 */
class ShowTimetable extends Component
{
    public Timetable $timetable;

    /**
     * Whether to print the timetable's own description above the calendar.
     */
    public bool $showDescription = true;

    /**
     * Whether to wrap the calendar in a card of its own.
     */
    public bool $showHeading = true;

    /**
     * Whether to show the navigable calendar instead of the print grid.
     */
    public bool $showCalendar = true;

    public string $audienceNote = '';

    public string $calendarView = 'month';

    public string $calendarDate = '';

    /** @var array<string, int> */
    public array $weekdayMap = [];

    /**
     * @var array<string, mixed>
     */
    public array $grid = [];

    /** @var Collection<int, TimetableTimeSlot>|null */
    private ?Collection $calendarSlotModels = null;

    protected TimetableCalendar $calendar;

    public function boot(TimetableCalendar $calendar): void
    {
        $this->calendar = $calendar;
    }

    public function mount(): void
    {
        $viewer = auth()->user();
        $period = $this->timetable->academicPeriod;
        $this->calendarDate = $period?->starts_on?->toDateString() ?? now()->toDateString();
        $this->weekdayMap = Weekday::query()->orderBy('id')->pluck('id', 'name')->all();
        $this->grid = $this->calendar->gridFor($this->timetable, Carbon::parse($this->calendarDate), $viewer);

        if ($viewer?->hasRole(Role::Teacher)) {
            $this->audienceNote = 'Showing subjects assigned to you, plus events for your role.';
        } elseif ($viewer?->hasRole(Role::Student)) {
            $this->audienceNote = 'Showing subjects you take, plus events for your role.';
        }
    }

    public function setCalendarView(string $view): void
    {
        if (!in_array($view, ['day', 'week', 'month'], true)) {
            return;
        }

        $this->calendarView = $view;
        $this->refreshCalendar();
    }

    public function moveCalendar(int $direction): void
    {
        $date = Carbon::parse($this->calendarDate);
        $this->calendarDate = match ($this->calendarView) {
            'month' => $date->addMonthsNoOverflow($direction)->toDateString(),
            'day' => $date->addDays($direction)->toDateString(),
            default => $date->addWeeks($direction)->toDateString(),
        };

        $this->refreshCalendar();
    }

    public function previousCalendarPeriod(): void
    {
        $this->moveCalendar(-1);
    }

    public function nextCalendarPeriod(): void
    {
        $this->moveCalendar(1);
    }

    public function goToCalendarToday(): void
    {
        $this->calendarDate = now()->toDateString();
        $this->refreshCalendar();
    }

    public function chooseCalendarDate(string $date): void
    {
        $this->calendarDate = Carbon::parse($date)->toDateString();
        $this->refreshCalendar();
    }

    /**
     * Build the month as calendar weeks.
     *
     * @return array<int, array<int, array{date: string, day: int, in_month: bool, in_period: bool, events: array<int, array<string, mixed>>}>>
     */
    #[Computed]
    public function monthWeeks(): array
    {
        $month = Carbon::parse($this->calendarDate)->startOfMonth();
        $cursor = $month->copy()->startOfWeek(Carbon::MONDAY);
        $period = $this->timetable->academicPeriod;
        $weeks = [];

        for ($week = 0; $week < 6; $week++) {
            $days = [];

            for ($day = 0; $day < 7; $day++) {
                $date = $cursor->copy();
                $days[] = [
                    'date' => $date->toDateString(),
                    'day' => $date->day,
                    'in_month' => $date->month === $month->month && $date->year === $month->year,
                    'in_period' => $this->isInCalendarPeriod($period, $date),
                    'events' => $this->eventsForDate($date),
                ];
                $cursor->addDay();
            }

            $weeks[] = $days;
        }

        return $weeks;
    }

    /**
     * Build the seven days around the selected date.
     *
     * @return array<int, array{date: string, day: int, name: string, short: string, in_period: bool, events: array<int, array<string, mixed>>}>
     */
    #[Computed]
    public function weekDays(): array
    {
        $start = Carbon::parse($this->calendarDate)->startOfWeek(Carbon::MONDAY);
        $period = $this->timetable->academicPeriod;
        $days = [];

        foreach (array_keys($this->weekdayMap) as $weekdayIndex => $name) {
            $date = $start->copy()->addDays($weekdayIndex);
            $days[] = [
                'date' => $date->toDateString(),
                'day' => $date->day,
                'name' => $name,
                'short' => substr($name, 0, 3),
                'in_period' => $this->isInCalendarPeriod($period, $date),
                'events' => $this->eventsForDate($date),
            ];
        }

        return $days;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function dayEvents(): array
    {
        return $this->eventsForDate(Carbon::parse($this->calendarDate));
    }

    public function calendarHeading(): string
    {
        $date = Carbon::parse($this->calendarDate);

        return match ($this->calendarView) {
            'day' => $date->format('l, j F Y'),
            'month' => $date->format('F Y'),
            default => $date->copy()->startOfWeek(Carbon::MONDAY)->format('j M').' – '.$date->copy()->endOfWeek(Carbon::SUNDAY)->format('j M Y'),
        };
    }

    public function render(): View
    {
        return view('livewire.show-timetable');
    }

    private function refreshCalendar(): void
    {
        $this->timetable->refresh();
        $this->calendarSlotModels = null;
        $this->grid = $this->calendar->gridFor(
            $this->timetable,
            Carbon::parse($this->calendarDate),
            auth()->user(),
        );
        unset($this->monthWeeks, $this->weekDays, $this->dayEvents);
    }

    /**
     * @return Collection<int, TimetableTimeSlot>
     */
    private function calendarSlots(): Collection
    {
        return $this->calendarSlotModels ??= $this->timetable->timeSlots()->get()->keyBy('id');
    }

    /**
     * @return array<int, array{key: string, time: string, name: string, kind: string|null, audience_role: string|null}>
     */
    private function eventsForDate(Carbon $date): array
    {
        return $this->calendar->eventsForDate(
            $this->timetable,
            $this->grid,
            $this->calendarSlots(),
            $this->weekdayMap,
            $date,
        );
    }

    private function isInCalendarPeriod(?AcademicPeriod $period, Carbon $date): bool
    {
        return $period === null
            || $period->starts_on === null
            || $period->ends_on === null
            || $period->covers($date);
    }
}
