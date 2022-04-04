<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Timetable;
use App\Services\MyClass\MyClassService;

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
