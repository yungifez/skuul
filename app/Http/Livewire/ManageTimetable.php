<?php

namespace App\Http\Livewire;

use App\Models\Timetable;
use App\Models\Weekday;
use App\Services\Timetable\TimetableService;
use Livewire\Component;

class ManageTimetable extends Component
{
    public Timetable $timetable;
    public $timeSlots;
    public ?int $timeSlot;
    public $weekdays;
    public $subjects;
    public $customItems;
    public $types;
    public $type;

    public function mount(TimetableService $timetableService)
    {
        $this->timeSlots = $this->timetable->timeSlots->sortBy('start_time')->load('weekdays');
        if ($this->timeSlots->isNotEmpty()) {
            $this->timeSlot = $this->timeSlots->first()->id;
        }
        $this->weekdays = Weekday::all();
        $this->subjects = $this->timetable->MyClass->subjects;
        $this->customItems = $timetableService->getAllCustomTimetableItem();
        $this->types = ['subject', 'customTimetableItem'];
        $this->type = $this->types[0];
    }

    public function render()
    {
        return view('livewire.manage-timetable');
    }
}
