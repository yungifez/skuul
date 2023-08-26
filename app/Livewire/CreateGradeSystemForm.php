<?php

namespace App\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class CreateGradeSystemForm extends Component
{
    public $classGroups;

    public function mount(MyClassService $myClassService)
    {
        $this->classGroups = $myClassService->getAllClassGroups();
    }

    public function render()
    {
        return view('livewire.create-grade-system-form');
    }
}
