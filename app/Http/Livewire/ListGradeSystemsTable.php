<?php

namespace App\Http\Livewire;

use App\Services\GradeSystem\GradeSystemService;
use App\Services\MyClass\MyClassService;
use Livewire\Component;

class ListGradeSystemsTable extends Component
{
    public $classGroups;
    public $classGroup;
    public $grades;

    protected $rules = [
        'classGroup' => 'integer',
    ];

    public function mount(MyClassService $myClassService, GradeSystemService $gradeSystemService)
    {
        // Get all class groups
        $this->classGroups = $myClassService->getAllClassGroups();

        // Get all grades for first class group if class groups is not empty
        if ($this->classGroups != null && $this->classGroups->count() > 0) {
            //class groups are present
            $this->classGroup = $this->classGroups[0]->id;
            $this->grades = $gradeSystemService->getAllGradesInClassGroup($this->classGroup)->load('classGroup')->sortBy('grade_till');
        } else {
            //if no class groups are present
            $this->classGroup = null;
            $this->grades = collect();
        }
    }

    public function updatedClassGroup()
    {
        //get all grades for selected class group
        $this->grades = app(GradeSystemService::class)->getAllGradesInClassGroup($this->classGroup)->load('classGroup');
    }

    public function render()
    {
        return view('livewire.list-grade-systems-table');
    }
}
