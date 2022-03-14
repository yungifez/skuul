<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use App\Services\MyClass\MyClassService;
use App\Services\Syllabus\SyllabusService;

class ListSyllabiTable extends Component
{
    public $class;
    public function mount(SyllabusService $syllabusService,MyClassService $myClassService)
    {
        $semester = auth()->user()->school->semester_id;
        if (auth()->user()->hasRole('student')) {
            $class = auth()->user()->studentRecord->my_class_id;
            $this->syllabi = $syllabusService->getAllSyllabiInSemesterAndClass($semester,$class);
        }else {
            $this->classes = $myClassService->getAllClasses();
            $this->syllabi = $syllabusService->getAllSyllabiInSemesterAndClass($semester,$this->classes[0]['id']);
        }
    }

    public function updatedClass()
    {
        $semester = auth()->user()->school->semester_id;
        return $this->syllabi = collect(App::make(SyllabusService::class)->getAllSyllabiInSemesterAndClass($semester,$this->class));
    }

    public function render()
    {
        return view('livewire.list-syllabi-table');
    }
}
