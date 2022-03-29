<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Timetable;
use Illuminate\Support\Facades\App;
use App\Services\MyClass\MyClassService;

class EditTimetableForm extends Component
{
    public Timetable $timetable;
    public $class;
    public $classes;
    public $subject;
    public function mount(MyClassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();
        $this->class = $this->timetable->subject->myClass->id;
        $this->subject = $this->timetable->subject->id;
    }
    public function updatedClass()
    {
        $this->subjects = collect(App::make(MyClassService::class)->getClassById($this->class)->subjects);
    }
    public function loadInitialSubjects()
    {
        $this->subjects = collect(App::make(MyClassService::class)->getClassById($this->class)->subjects);
    }

    public function render()
    {
        return view('livewire.edit-timetable-form');
    }
}
