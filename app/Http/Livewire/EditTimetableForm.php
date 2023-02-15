<?php

namespace App\Http\Livewire;

use App\Models\Timetable;
use App\Services\MyClass\MyClassService;
use Livewire\Component;

class EditTimetableForm extends Component
{
    public Timetable $timetable;

    public $class;

    public $classes;

    public function mount(MyClassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();
        $this->class = $this->timetable->myClass->id;
    }

    public function render()
    {
        return view('livewire.edit-timetable-form');
    }
}
