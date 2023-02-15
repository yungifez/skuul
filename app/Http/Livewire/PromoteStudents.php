<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
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
        $this->oldSection = $this->oldSections->first()['id'] ?? null;
    }

    public function updatedNewClass()
    {
        $this->newSections = collect($this->classes->where('id', $this->newClass)->first()['sections']);
        $this->newSection = $this->newSections->first()['id'] ?? null;
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

        $this->students = App::make(SectionService::class)->getSectionById($this->oldSection)->students();
    }

    public function render()
    {
        return view('livewire.promote-students');
    }
}
