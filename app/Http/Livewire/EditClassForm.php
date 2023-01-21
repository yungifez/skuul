<?php

namespace App\Http\Livewire;

use App\Models\MyClass;
use Livewire\Component;
use App\Services\MyClass\MyClassService;

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
