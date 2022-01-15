<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;

class CreateSubjectForm extends Component
{
    public function mount(MyClassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();
    }
    public function render()
    {
        return view('livewire.create-subject-form');
    }
}
