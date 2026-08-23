<?php

namespace App\Livewire;

use App\Models\Timetable;
use App\Services\Timetable\TimetableGrid;
use Illuminate\View\View;
use Livewire\Component;

/**
 * The week of one timetable, read only.
 *
 * The builder draws the same week and lets people change it. This one is
 * what students, teachers, and the printed sheet read.
 */
class ShowTimetable extends Component
{
    public Timetable $timetable;

    /**
     * Whether to print the timetable's own description above the week.
     */
    public bool $showDescription = true;

    /**
     * Whether to wrap the week in a card of its own.
     *
     * The builder supplies its own heading, so it turns this off.
     */
    public bool $showHeading = true;

    /**
     * @var array<string, mixed>
     */
    public array $grid = [];

    public function mount(TimetableGrid $grid): void
    {
        $this->grid = $grid->of($this->timetable);
    }

    public function render(): View
    {
        return view('livewire.show-timetable');
    }
}
