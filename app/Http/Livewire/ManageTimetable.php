<?php

namespace App\Http\Livewire;

use App\Models\Timetable;
use App\Models\Weekday;
use App\Services\Timetable\TimetableService;
use Livewire\Component;

class ManageTimetable extends Component
{
    protected $listeners = ['timetableCellClicked' => 'setSelectFields'];

    protected $queryString = ['weekday', 'type', 'timeSlot'];

    public Timetable $timetable;

    public $timeSlots;

    public $timeSlot;

    public $weekdays;

    public $weekday;

    public $subjects;

    public $customItems;

    public $types;

    public $type;

    public function mount(TimetableService $timetableService)
    {
        $this->timeSlots = $this->timetable->timeSlots->sortBy('start_time')->load('weekdays');
        if ($this->timeSlots->isNotEmpty()) {
            $this->timeSlot ?? $this->timeSlot = $this->timeSlots->first()->id;
        }
        $this->weekdays = Weekday::all();
        $this->subjects = $this->timetable->MyClass->subjects;
        $this->customItems = $timetableService->getAllCustomTimetableItem();
        $this->types = ['subject', 'customTimetableItem'];
        $this->type = $this->type ?? $this->types[0];
    }

    public function setSelectFields($records)
    {
        $this->timeSlot = $records['timeSlot']['id'];
        $this->weekday = $records['weekday']['id'];
    }

    public function render()
    {
        return view('livewire.manage-timetable');
    }
}
