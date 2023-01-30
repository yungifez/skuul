<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

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
