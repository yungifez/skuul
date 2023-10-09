<?php

namespace App\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class CreateSectionForm extends Component
{
    public $myClasses;

    public function mount(MyClassService $classService)
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());

        $this->myClasses = $classService->getAllClasses();
    }

    public function render()
    {
        return view('livewire.create-section-form');
    }
}
