<?php

namespace App\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class CreateClassForm extends Component
{
    public $classGroups;

    public function mount(MyClassService $myClassService)
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
        $this->classGroups = $myClassService->getAllClassGroups();
    }

    public function render()
    {
        return view('livewire.create-class-form');
    }
}
