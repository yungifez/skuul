<?php

namespace App\Livewire;

use App\Exceptions\InvalidValueException;
use App\Models\CustomTimetableItem;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\Weekday;
use App\Services\Timetable\TimeSlotService;
use App\Services\Timetable\TimetableConflictChecker;
use App\Services\Timetable\TimetableGrid;
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

    /**
     * The cell being worked on, as "timeSlotId:weekdayId".
     */
    public ?string $selected = null;

    /**
     * Narrows the list of subjects and breaks to choose from.
     */
    public string $search = '';

    public string $startTime = '';

    public string $stopTime = '';

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
        $this->refreshWeek();
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

        $this->write(fn () => $timeSlots->placeRecord($cell[0], $cell[1], $kind, $recordableId));
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
        ], attributes: [
            'startTime' => 'start time',
            'stopTime' => 'end time',
        ]);

        $this->write(function () use ($timeSlots): void {
            $timeSlots->createTimeSlot([
                'start_time' => $this->startTime,
                'stop_time' => $this->stopTime,
                'timetable_id' => $this->timetable->id,
            ]);

            $this->startTime = '';
            $this->stopTime = '';
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
            ->whereHas(
                'courseOfferings.cycleSections',
                fn ($query) => $query->whereKey($this->timetable->academic_cycle_section_id),
            )
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
        $this->grid = app(TimetableGrid::class)->of($this->timetable);
        $this->conflicts = app(TimetableConflictChecker::class)->conflicts($this->timetable);
        unset($this->timeSlots, $this->selectedLabel);
    }
}
