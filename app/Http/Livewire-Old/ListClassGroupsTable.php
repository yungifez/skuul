<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class ListClassGroupsTable extends Component
{
    public $classGroups;

    public function mount(MyClassService $myClassService)
    {
        $this->classGroups = $myClassService->getAllClassGroups();
    }

    public function render()
    {
        return view('livewire.list-class-groups-table');
    }
}
