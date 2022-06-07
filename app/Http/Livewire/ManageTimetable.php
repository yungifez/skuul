<?php

namespace App\Http\Livewire;

use App\Models\Timetable;
use App\Models\Weekday;
use Livewire\Component;

class ManageTimetable extends Component
{
    public Timetable $timetable;
    public $timeSlots;
    public $weekdays;
    public $subjects;

    public function mount()
    {
        $this->timeSlots = $this->timetable->timeSlots->sortBy('start_time');
        $this->weekdays = Weekday::all();
        $this->subjects = $this->timetable->MyClass->subjects;
    }

    public function render()
    {
        return view('livewire.manage-timetable');
    }
}
