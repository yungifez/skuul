<?php

namespace App\Livewire;

use App\Models\MyClass;
use App\Services\MyClass\MyClassService;
use Livewire\Component;

class EditClassForm extends Component
{
    public MyClass $myClass;

    public $classGroups;

    public function mount(MyClassService $myClassService)
    {
        $this->classGroups = $myClassService->getAllClassGroups();
    }

    public function render()
    {
        return view('livewire.edit-class-form');
    }
}
