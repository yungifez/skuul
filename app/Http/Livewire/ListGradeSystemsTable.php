<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class ListGradeSystemsTable extends Component
{
    protected $queryString = ['classGroup'];

    public $classGroups;

    public $classGroup;

    protected $rules = [
        'classGroup' => 'integer',
    ];

    public function mount(MyClassService $myClassService)
    {
        // Get all class groups
        $this->classGroups = $myClassService->getAllClassGroups();

        if (auth()->user()->hasRole('student')) {
            $this->classGroup = auth()->user()->studentRecord->myClass->ClassGroup->id;
        }

        // Get all grades for first class group if class groups is not empty
        if ($this->classGroups != null && $this->classGroups->count() > 0) {
            //class groups are present
            $this->updatedClassGroup();
        }
    }

    public function updatedClassGroup()
    {
        if ($this->classGroups->find($this->classGroup) == null) {
            $this->classGroup = $this->classGroups?->first()->id;
        }

        $this->emit('$refresh');
    }

    public function render()
    {
        return view('livewire.list-grade-systems-table');
    }
}
