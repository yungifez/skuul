<?php

namespace App\Http\Livewire;

use App\Models\Weekday;
use Livewire\Component;
use App\Models\Timetable;

class ShowTimetable extends Component
{
    public Timetable $timetable;

    public function mount()
    {
        $this->timeSlots = $this->timetable->timeSlots->sortBy('start_time');
        $this->weekdays = Weekday::all();
        $this->subjects = $this->timetable->MyClass->subjects;
    }
    
    public function render()
    {
        return view('livewire.show-timetable');
    }
}
