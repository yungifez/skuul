<?php

namespace App\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class CreateTimetableForm extends Component
{
    public $class;

    public $classes;

    public function mount(MyClassService $myClassService)
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());

        $this->classes = $myClassService->getAllClasses();
    }

    public function render()
    {
        return view('livewire.create-timetable-form');
    }
}
