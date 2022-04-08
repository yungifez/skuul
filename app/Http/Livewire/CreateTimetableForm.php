<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use App\Services\MyClass\MyClassService;

class CreateTimetableForm extends Component
{
    public $class;
    public $classes;
    public function mount(MyClassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();
    }

    public function render()
    {
        return view('livewire.create-timetable-form');
    }
}
