<?php

namespace App\Livewire;

use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
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
        if ($this->sections->isNotEmpty()) {
            $this->section = $this->sections->first()['id'];
        }
    }

    public function loadInitialSections()
    {
        $this->updatedClass();
    }

    public function loadStudents()
    {
        $this->validate();

        $this->students = App::make(SectionService::class)->getSectionById($this->section)->students();
    }

    public function render()
    {
        return view('livewire.graduate-students');
    }
}
