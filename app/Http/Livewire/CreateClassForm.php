<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;

class CreateClassForm extends Component
{
    public $classGroups;

    public function mount(MyClassService $myClassService)
    {
        $this->classGroups = $myClassService->getAllClassGroups();
    }
    public function render()
    {
        return view('livewire.create-class-form');
    }
}
