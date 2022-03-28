<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use App\Services\MyClass\MyClassService;
use App\Services\TimeTable\TimetableService;

class ListTimetablesTable extends Component
{
    public $class;
    public function mount(TimetableService $timetableService, MyClassService $myClassService)
    {
        $semester = auth()->user()->school->semester_id;
        if (auth()->user()->hasRole('student')) {
            $this->class = auth()->user()->studentRecord->myClass->name;
            $class = auth()->user()->studentRecord->my_class_id;
            $this->syllabi = $timetableService->getAllTimetablesInSemesterAndClass($semester,$class);
        }else {
            $this->classes = $myClassService->getAllClasses();
            $this->timetables = $timetableService->getAllTimetablesInSemesterAndClass($semester,$this->classes[0]['id']);
        }
    }

    public function updatedClass()
    {
        $semester = auth()->user()->school->semester_id;
        $this->timestable = collect(App::make(TimetableService::class)->getAllTimetablesInSemesterAndClass($semester,$this->class));
    }

    public function render()
    {
        return view('livewire.list-timetables-table');
    }
}
