<?php

namespace App\Http\Livewire;

use App\Models\Timetable;
use App\Models\Weekday;
use App\Services\Timetable\TimetableService;
use Livewire\Component;

class ShowTimetable extends Component
{
    public Timetable $timetable;
    public $weekDays;
    public $subjects;
    public $customItems;

    public function mount(TimetableService $timetableService)
    {
        $this->timeSlots = $this->timetable->timeSlots->sortBy('start_time')->load('weekdays' );
        // dd($this->timeSlots);
        $this->weekdays = Weekday::all();
        $this->subjects = $this->timetable->MyClass->subjects;
        $this->customItems = $timetableService->getAllCustomTimetableItem();
    }

    public function render()
    {
        return view('livewire.show-timetable');
    }
}
