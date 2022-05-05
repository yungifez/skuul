<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\GradeSystem;
use App\Services\MyClass\MyClassService;

class EditGradeSystemForm extends Component
{
    public GradeSystem $grade;
    public $classGroups;

    public function mount(MyClassService $myClassService)
    {
        $this->classGroups = $myClassService->getAllClassGroups();
    }
    public function render()
    {
        return view('livewire.edit-grade-system-form');
    }
}
