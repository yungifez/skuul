<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class GraduateStudents extends Component
{
    public $classes;
    public $class;
    public $sections;
    public $section;
    public $students;

    protected $rules = [
        'class'   => 'required|exists:my_classes,id',
        'section' => 'required|exists:sections,id',
    ];

    public function mount(MyClassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();  //set default values
        $this->class = $this->classes[0]->id;
    }

    public function updatedClass()
    {
        $this->sections = collect($this->classes->where('id', $this->class)->first()['sections']);
    }

    public function loadInitialSections()
    {
        $this->sections = collect($this->classes->first()['sections']);
        $this->section = $this->sections->first()['id'];
    }

    public function loadStudents()
    {
        $this->validate();

        $this->students = App::make(StudentService::class)->getAllStudents()->load('studentRecord')->filter(function ($student) {
            return $student->studentRecord->my_class_id == $this->class && $student->studentRecord->section_id == $this->section;
        });
    }

    public function render()
    {
        return view('livewire.graduate-students');
    }
}
