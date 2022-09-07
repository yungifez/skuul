<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class PromoteStudents extends Component
{
    public $classes;
    public $oldClass;
    public $oldSections;
    public $oldSection;
    public $newClass;
    public $newSections;
    public $newSection;
    public $students;

    protected $rules = [
        'oldClass'   => 'required|exists:my_classes,id',
        'oldSection' => 'required|exists:sections,id',
        'newClass'   => 'required|exists:my_classes,id',
        'newSection' => 'required|exists:sections,id',
    ];

    public function mount(MyclassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();

        //set default values
        if ($this->classes->isNotEmpty()) {
            $this->oldClass = $this->classes[0]->id;
            $this->newClass = $this->classes[0]->id;

            //load initial sections
            $this->loadInitialNewSections();
            $this->loadInitialOldSections();
        }
    }

    public function updatedOldClass()
    {
        $this->oldSections = collect($this->classes->where('id', $this->oldClass)->first()['sections']);
    }

    public function updatedNewClass()
    {
        $this->newSections = collect($this->classes->where('id', $this->newClass)->first()['sections']);
    }

    public function loadInitialOldSections()
    {
        $this->oldSections = collect($this->classes->first()['sections']);
        if ($this->oldSections->isNotEmpty()) {
            $this->oldSection = $this->oldSections->first()['id'];
        }
    }

    public function loadInitialNewSections()
    {
        $this->newSections = collect($this->classes->first()['sections']);
        if ($this->newSections->isNotEmpty()) {
            $this->newSection = $this->newSections->first()['id'];
        }
    }

    public function loadStudents()
    {
        $this->validate();

        // it was with this line of code that I knew ive run mad
        // $this->students = App::make(MyClassService::class)->getClassById($this->oldClass)->sections->where('id', $this->oldSection)->load('studentRecords')->first()->studentRecords->load('user');

        $this->students = App::make(StudentService::class)->getAllStudents()->load('studentRecord')->filter(function ($student) {
            return $student->studentRecord->my_class_id == $this->oldClass && $student->studentRecord->section_id == $this->oldSection;
        });
    }

    public function render()
    {
        return view('livewire.promote-students');
    }
}
