<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class CreateSyllabusForm extends Component
{
    public $class;

    public $classes;

    public $subject;

    public $subjects;

    public function mount(MyClassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();
    }

    public function updatedClass()
    {
        $this->subjects = collect(App::make(MyClassService::class)->getClassById($this->class)->subjects);
    }

    public function loadInitialSubjects()
    {
        $this->subjects = collect(App::make(MyClassService::class)->getClassById($this->classes[0]['id'])->subjects);
    }

    public function render()
    {
        return view('livewire.create-syllabus-form');
    }
}
