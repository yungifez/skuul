<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use App\Services\Syllabus\SyllabusService;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class ListSyllabiTable extends Component
{
    public $class;

    public function mount(SyllabusService $syllabusService, MyClassService $myClassService)
    {
        $semester = auth()->user()->school->semester_id;
        if (auth()->user()->hasRole('student')) {
            $this->class = auth()->user()->studentRecord->myClass->name;
            $class = auth()->user()->studentRecord->my_class_id;
            $this->syllabi = $syllabusService->getAllSyllabiInSemesterAndClass($semester, $class)->load('subject');
        } else {
            $this->classes = $myClassService->getAllClasses();
            //make sure classes arent empty
            if (!$this->classes->isEmpty()) {
                $this->syllabi = $syllabusService->getAllSyllabiInSemesterAndClass($semester, $this->classes[0]['id'])->load('subject');
            } else {
                $this->classes = [];
                $this->syllabi = collect([]);
            }

            if ($this->syllabi->isEmpty()) {
                $this->syllabi = null;
            }
        }
    }

    public function updatedClass()
    {
        $semester = auth()->user()->school->semester_id;
        $this->syllabi = collect(App::make(SyllabusService::class)->getAllSyllabiInSemesterAndClass($semester, $this->class));

        if ($this->syllabi->isEmpty()) {
            $this->syllabi = null;
        }
    }

    public function render()
    {
        return view('livewire.list-syllabi-table');
    }
}
