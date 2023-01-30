<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class CreateSectionForm extends Component
{
    public $myClasses;

    public function mount(MyClassService $classService)
    {
        $this->myClasses = $classService->getAllClasses();
    }

    public function render()
    {
        return view('livewire.create-section-form');
    }
}
