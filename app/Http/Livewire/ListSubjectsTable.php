<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;
use App\Services\Subject\SubjectService;

class ListSubjectsTable extends Component
{
    public function mount(MyClassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();
    }
    public function render()
    {
        return view('livewire.list-subjects-table');
    }
}
