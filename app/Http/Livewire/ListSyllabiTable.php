<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;

class ListSyllabiTable extends Component
{
    protected $queryString = ['class'];
    public $class;
    public $classes;

    public function mount( MyClassService $myClassService)
    {
        if (auth()->user()->hasRole('student')) {
            return $this->class = auth()->user()->studentRecord->myClass->id;   
        } 

        $this->classes = $myClassService->getAllClasses();
        if ($this->classes->isNotEmpty()) {
            $this->updatedClass();
        }
    }

    public function updatedClass()
    {
        if ($this->classes->find($this->class) == null) {
            $this->class = $this->classes?->first()->id;
        }

        $this->emit('$refresh');
    }
    
    public function render()
    {
        return view('livewire.list-syllabi-table');
    }
}
