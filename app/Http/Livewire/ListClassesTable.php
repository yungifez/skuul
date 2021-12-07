<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;

class ListClassesTable extends Component
{
    public $myClasses;
    public function mount(MyClassService $myClass)
    {
        $this->myClasses = $myClass->getAllClasses();
    }
    
    public function render()
    {
        return view('livewire.list-classes-table');
    }
}
