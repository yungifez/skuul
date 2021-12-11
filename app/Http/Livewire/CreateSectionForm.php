<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;

class CreateSectionForm extends Component
{
    public $myClasses;

    //assign value of $myClasses to the value of $classService->getAllClasses()

    public function mount(MyClassService $classService)
    {
        $this->myClasses = $classService->getAllClasses();
    }

    public function render()
    {
        return view('livewire.create-section-form');
    }
}
