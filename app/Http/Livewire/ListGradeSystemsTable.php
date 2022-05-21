<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;
use App\Services\GradeSystem\GradeSystemService;

class ListGradeSystemsTable extends Component
{
    public $classGroups;
    public $classGroup;
    public $grades;

    protected $rules = [
        'classGroup' => 'integer'
    ];

    public function mount(MyClassService $myClassService, GradeSystemService $gradeSystemService)
    {
        $this->classGroups = $myClassService->getAllClassGroups();
        if ($this->classGroups != null) {
            $this->classGroup = $this->classGroups[0]->id;  
            $this->grades = $gradeSystemService->getAllGradesInClassGroup($this->classGroup)->load('classGroup')->sortBy('grade_to');
        }else {
            //if no class gorups are present
            $this->classGroup = null;
            $this->grades = [];
        }
    }

    public function updatedClassGroup()
    {
        $this->grades = app(GradeSystemService::class)->getAllGradesInClassGroup($this->classGroup)->load('classGroup');
    }
    public function render()
    {
        return view('livewire.list-grade-systems-table');
    }
}
