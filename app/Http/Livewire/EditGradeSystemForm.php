<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\GradeSystem;
use App\Services\MyClass\MyClassService;

class EditGradeSystemForm extends Component
{
    public GradeSystem $grade;
    public $classGroups, $classGroup;

    public function mount(MyClassService $myClassService)
    {
        $this->classGroups = $myClassService->getAllClassGroups();
        $this->classGroup = $this->grade->class_group_id;
    }
    public function render()
    {
        return view('livewire.edit-grade-system-form');
    }
}
