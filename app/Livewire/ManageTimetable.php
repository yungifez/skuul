<?php

namespace App\Livewire;

use App\Exceptions\InvalidValueException;
use App\Models\CustomTimetableItem;
use App\Models\Facility;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\Weekday;
use App\Services\Timetable\TimeSlotService;
use App\Services\Timetable\TimetableConflictChecker;
use App\Services\Timetable\TimetableGrid;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Build the week of a draft timetable.
 *
 * Choose a cell, then choose what goes in it. Nothing here reloads the page,
 * so placing a week of lessons stays one continuous task. Only a draft
 * reaches this screen, because a published timetable is a promise the school
 * has already made.
 */
class ManageTimetable extends Component
{
    public Timetable $timetable;

    public string $calendarView = 'week';

    public string $calendarDate = '';

    /** @var array<string, int> */
    public array $weekdayMap = [];

    /**
     * The cell being worked on, as "timeSlotId:weekdayId".
     */
    public ?string $selected = null;

    /**
     * Narrows the list of subjects and breaks to choose from.
     */
    public string $search = '';

    public ?int $facilityId = null;

    public string $startTime = '';

    public string $stopTime = '';

    public string $slotRecurrence = 'weekly';

    public int $slotRecurrenceInterval = 1;

    public string $slotStartsOn = '';

    public ?string $slotOccursOn = null;

    /** @var array<int, int> */
    public array $slotWeekdayIds = [];

    /**
     * @var array<string, mixed>
     */
    public array $grid = [];

    /**
     * @var array<int, string>
     */
    public array $conflicts = [];

    public function mount(): void
    {
        $period = $this->timetable->academicPeriod;
        $this->calendarDate = $period?->starts_on?->toDateString() ?? now()->toDateString();
        $this->slotStartsOn = $this->calendarDate;
        $this->slotOccursOn = $this->calendarDate;
        $this->weekdayMap = Weekday::query()->pluck('id', 'name')->all();
        $this->slotWeekdayIds = [$this->weekdayMap[Carbon::parse($this->slotStartsOn)->englishDayOfWeek] ?? 1];
        $this->refreshWeek();
    }

    public function setCalendarView(string $view): void
    {
        if (in_array($view, ['day', 'week', 'month'], true)) {
            $this->calendarView = $view;
            $this->refreshWeek();
        }
    }

    public function moveCalendar(int $direction): void
    {
        $date = Carbon::parse($this->calendarDate);
        $this->calendarDate = ($this->calendarView === 'month'
            ? $date->addMonths($direction)
            : $date->addWeeks($direction)
        )->toDateString();
        $this->slotOccursOn = $this->calendarDate;
        $this->refreshWeek();
    }

    public function goToCalendarToday(): void
    {
        $this->calendarDate = now()->toDateString();
        $this->slotOccursOn = $this->calendarDate;
        $this->refreshWeek();
    }

    public function chooseCalendarDate(string $date): void
    {
        $this->calendarDate = Carbon::parse($date)->toDateString();
        $this->slotRecurrence = 'one_time';
        $this->slotOccursOn = $this->calendarDate;
        $this->calendarView = 'day';
        $this->refreshWeek();
    }

    public function updatedSlotStartsOn(?string $date): void
    {
        if ($this->slotRecurrence === 'weekly' && $date !== null && $date !== '') {
            $this->slotWeekdayIds = [$this->weekdayMap[Carbon::parse($date)->englishDayOfWeek] ?? 1];
        }
    }

    /**
     * Build the month as calendar weeks, including the events that fall on
     * each date. Recurring entries remain term-scoped because the timetable
     * owns the academic period rather than a copied end date.
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
                    'in_month' => $date->month === $month->month,
                    'in_period' => $period?->covers($date) ?? false,
                    'events' => $this->eventsForDate($date),
                ];
                $cursor->addDay();
            }

            $weeks[] = $days;
        }

        return $weeks;
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

    public function slotRuleLabel(TimetableTimeSlot $slot): string
    {
        if ($slot->recurrence === 'one_time') {
            return 'One date · '.($slot->occurs_on?->format('j M Y') ?? 'date missing');
        }

        $unit = $slot->recurrence === 'monthly' ? 'month' : 'week';
        $interval = (int) $slot->recurrence_interval;
        $frequency = 'Every '.($interval === 1 ? '' : $interval.' ').$unit.($interval === 1 ? '' : 's');

        if ($slot->recurrence === 'weekly' && filled($slot->recurrence_weekdays)) {
            $names = array_flip($this->weekdayMap);
            $days = collect($slot->recurrence_weekdays)
                ->map(fn (int $weekdayId): ?string => $names[$weekdayId] ?? null)
                ->filter()
                ->implode(', ');
            $frequency .= ' on '.$days;
        } elseif ($slot->starts_on !== null) {
            $frequency .= ' on the '.$slot->starts_on->format('jS');
        }

        return $frequency.' from '.($slot->starts_on?->format('j M Y') ?? 'the term start');
    }

    /**
     * Choose the cell to fill or empty.
     */
    public function selectCell(int $timeSlotId, int $weekdayId): void
    {
        $key = $timeSlotId.':'.$weekdayId;
        // Clicking the chosen cell again puts the picker away.
        $this->selected = $this->selected === $key ? null : $key;
    }

    /**
     * Put a subject or a break in the chosen cell.
     */
    public function assign(string $kind, int $recordableId, TimeSlotService $timeSlots): void
    {
        $this->authorize('update', $this->timetable);

        $cell = $this->selectedCell();

        if ($cell === null) {
            return;
        }

        $this->write(fn () => $timeSlots->placeRecord($cell[0], $cell[1], $kind, $recordableId, $this->facilityId));
    }

    /**
     * Get the shared places a lesson can be moved into.
     *
     * @return Collection<int, Facility>
     */
    public function facilities()
    {
        return Facility::inSchool()
            ->active()
            ->holdsLessons()
            ->orderBy('name')
            ->get();
    }

    /**
     * Empty the chosen cell.
     */
    public function clearCell(TimeSlotService $timeSlots): void
    {
        $this->authorize('update', $this->timetable);

        $cell = $this->selectedCell();

        if ($cell === null) {
            return;
        }

        $this->write(fn () => $timeSlots->clearRecord($cell[0], $cell[1]));
    }

    /**
     * Add one time slot to the week.
     */
    public function addTimeSlot(TimeSlotService $timeSlots): void
    {
        $this->authorize('update', $this->timetable);

        $this->validate([
            'startTime' => ['required', 'date_format:H:i'],
            'stopTime' => ['required', 'date_format:H:i', 'after:startTime'],
            'slotRecurrence' => ['required', 'in:weekly,monthly,one_time'],
            'slotRecurrenceInterval' => ['required_unless:slotRecurrence,one_time', 'integer', 'min:1', 'max:52'],
            'slotStartsOn' => ['required_unless:slotRecurrence,one_time', 'nullable', 'date'],
            'slotWeekdayIds' => ['required_if:slotRecurrence,weekly', 'array', 'min:1'],
            'slotWeekdayIds.*' => ['integer', 'exists:weekdays,id'],
            'slotOccursOn' => ['required_if:slotRecurrence,one_time', 'nullable', 'date'],
        ], attributes: [
            'startTime' => 'start time',
            'stopTime' => 'end time',
        ]);

        $date = $this->slotRecurrence === 'one_time' ? $this->slotOccursOn : $this->slotStartsOn;
        $dateAttribute = $this->slotRecurrence === 'one_time' ? 'slotOccursOn' : 'slotStartsOn';

        if (!$this->validSlotDate($date, $dateAttribute)) {
            return;
        }

        $this->slotWeekdayIds = array_values(array_unique(array_map('intval', $this->slotWeekdayIds)));

        $this->write(function () use ($timeSlots): void {
            $timeSlots->createTimeSlot([
                'start_time' => $this->startTime,
                'stop_time' => $this->stopTime,
                'timetable_id' => $this->timetable->id,
                'recurrence' => $this->slotRecurrence,
                'occurs_on' => $this->slotRecurrence === 'one_time' ? $this->slotOccursOn : null,
                'starts_on' => $this->slotRecurrence === 'one_time' ? null : $this->slotStartsOn,
                'recurrence_interval' => $this->slotRecurrence === 'one_time' ? 1 : $this->slotRecurrenceInterval,
                'recurrence_weekdays' => $this->slotRecurrence === 'weekly' ? $this->slotWeekdayIds : null,
            ]);

            $this->startTime = '';
            $this->stopTime = '';
            $this->slotRecurrence = 'weekly';
            $this->slotRecurrenceInterval = 1;
            $this->slotStartsOn = $this->timetable->academicPeriod?->starts_on?->toDateString() ?? now()->toDateString();
            $this->slotOccursOn = $this->timetable->academicPeriod?->starts_on?->toDateString();
            $this->slotWeekdayIds = [$this->weekdayMap[Carbon::parse($this->slotStartsOn)->englishDayOfWeek] ?? 1];
        });
    }

    /**
     * Take one time slot off the week, with everything placed in it.
     */
    public function removeTimeSlot(int $timeSlotId, TimeSlotService $timeSlots): void
    {
        $this->authorize('update', $this->timetable);

        $slot = $this->timetable->timeSlots()->whereKey($timeSlotId)->first();

        if ($slot === null) {
            return;
        }

        $this->write(function () use ($slot, $timeSlots): void {
            $timeSlots->deleteTimeSlot($slot);
            $this->selected = null;
        });
    }

    /**
     * Get the subjects this home group is taught.
     *
     * @return Collection<int, Subject>
     */
    #[Computed]
    public function subjects()
    {
        return Subject::query()
            ->when($this->timetable->academic_cycle_section_id !== null, fn ($query) => $query->whereHas(
                'courseOfferings.cycleSections',
                fn ($query) => $query->whereKey($this->timetable->academic_cycle_section_id),
            ))
            ->inSchool()
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Get the parts of the day that are not a lesson.
     *
     * @return Collection<int, CustomTimetableItem>
     */
    #[Computed]
    public function customItems()
    {
        return CustomTimetableItem::inSchool()
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Get the time slots of the week, earliest first.
     *
     * @return Collection<int, TimetableTimeSlot>
     */
    #[Computed]
    public function timeSlots()
    {
        return $this->timetable->timeSlots()->get()->sortBy('start_time')->values();
    }

    /**
     * Get one-date slots that need attention after a period date change.
     *
     * @return Collection<int, TimetableTimeSlot>
     */
    #[Computed]
    public function outOfPeriodSlots()
    {
        return $this->timetable->timeSlots()
            ->get()
            ->filter(fn (TimetableTimeSlot $slot): bool => $slot->occursOutsideAcademicPeriod())
            ->values();
    }

    /**
     * Say which cell is being worked on, in words.
     */
    #[Computed]
    public function selectedLabel(): ?string
    {
        $cell = $this->selectedCell();

        if ($cell === null) {
            return null;
        }

        $weekday = Weekday::query()->find($cell[1]);

        return $weekday === null
            ? null
            : sprintf('%s, %s to %s', $weekday->name, $cell[0]->start_time, $cell[0]->stop_time);
    }

    public function render(): View
    {
        return view('livewire.manage-timetable');
    }

    /**
     * Read the chosen cell as its time slot and weekday.
     *
     * @return array{0: TimetableTimeSlot, 1: int}|null
     */
    private function selectedCell(): ?array
    {
        if ($this->selected === null) {
            return null;
        }

        [$timeSlotId, $weekdayId] = array_map('intval', explode(':', $this->selected, 2));
        $slot = $this->timetable->timeSlots()->whereKey($timeSlotId)->first();

        return $slot === null ? null : [$slot, $weekdayId];
    }

    /**
     * Run one change to the week and draw the result.
     *
     * A published timetable refuses changes from the model itself, so the
     * refusal is shown on the screen instead of ending the request.
     */
    private function write(callable $change): void
    {
        try {
            $change();
        } catch (InvalidValueException $exception) {
            throw ValidationException::withMessages(['timetable' => $exception->getMessage()]);
        }

        $this->refreshWeek();
    }

    /**
     * Read the week and its clashes again.
     */
    private function refreshWeek(): void
    {
        $this->timetable->refresh();
        $this->grid = app(TimetableGrid::class)->of(
            $this->timetable,
            Carbon::parse($this->calendarDate)->startOfWeek(Carbon::MONDAY),
        );
        $this->conflicts = app(TimetableConflictChecker::class)->conflicts($this->timetable);
        unset($this->timeSlots, $this->selectedLabel, $this->monthWeeks, $this->dayEvents);
    }

    /**
     * Read all entries that occur on one actual date.
     *
     * @return array<int, array{key: string, time: string, name: string, kind: string, audience_role: string|null}>
     */
    private function eventsForDate(Carbon $date): array
    {
        $weekdayId = $this->weekdayMap[$date->englishDayOfWeek] ?? null;

        if ($weekdayId === null) {
            return [];
        }

        $events = [];

        foreach ($this->grid['rows'] as $row) {
            $cell = $row['cells'][$weekdayId] ?? null;

            if ($cell === null || !$cell['active'] || $cell['kind'] === null) {
                continue;
            }

            $events[] = [
                'key' => $row['id'].':'.$weekdayId,
                'time' => $row['start'].'–'.$row['stop'],
                'name' => $cell['name'],
                'kind' => $cell['kind'],
                'audience_role' => $cell['audience_role'],
            ];
        }

        return $events;
    }

    private function validSlotDate(?string $date, string $attribute): bool
    {
        $period = $this->timetable->academicPeriod;

        if ($date === null || $period === null) {
            $this->addError($attribute, 'Choose a date inside the selected academic period.');

            return false;
        }

        if ($period->starts_on === null || $period->ends_on === null) {
            return true;
        }

        $date = Carbon::parse($this->slotOccursOn);

        if ($date->lt($period->starts_on) || $date->gt($period->ends_on)) {
            $this->addError($attribute, 'The date must be inside the selected academic period.');

            return false;
        }

        return true;
    }
}
