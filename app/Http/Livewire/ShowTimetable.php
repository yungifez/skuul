<?php

namespace App\Http\Livewire;

use App\Models\Weekday;
use Livewire\Component;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Services\Timetable\TimetableService;
use Illuminate\Console\View\Components\Warn;

class ShowTimetable extends Component
{
    public Timetable $timetable;
    public $weekDays;
    public $subjects;
    public $customItems;
    public bool $disableEmitCellInformationDetail = true;

    /**
     * Determine if to show the timetable
     *
     * @var boolean
     */
    public bool $showDescription = true;

    public function mount(TimetableService $timetableService)
    {
        $this->timeSlots = $this->timetable->timeSlots->sortBy('start_time')->load('weekdays');
        // dd($this->timeSlots);
        $this->weekdays = Weekday::all();
        $this->subjects = $this->timetable->load('myClass')->MyClass->subjects;
        $this->customItems = $timetableService->getAllCustomTimetableItem();
    }

    public function emitCellInformationDetail(TimetableTimeSlot $timeSlot, Weekday $weekday)
    {
        $this->emit('timetableCellClicked', ['timeSlot' => $timeSlot, 'weekday' => $weekday]);
    }

    public function render()
    {
        return view('livewire.show-timetable');
    }
}
