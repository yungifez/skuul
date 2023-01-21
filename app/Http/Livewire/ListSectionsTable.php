<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;

class ListSectionsTable extends Component
{
    protected $queryString = ['class'];
    public $classes;
    public $class;

    public function mount(MyClassService $myClassService)
    {
        $this->classes = $myClassService->getAllClasses();
        $this->updatedClass();
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
        return view('livewire.list-sections-table');
    }
}
