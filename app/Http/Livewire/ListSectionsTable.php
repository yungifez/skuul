<?php

namespace App\Http\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class ListSectionsTable extends Component
{
    public $myClasses;

    public function mount(MyClassService $myClassService)
    {
        $this->myClasses = $myClassService->getAllClasses();
    }

    public function render()
    {
        return view('livewire.list-sections-table');
    }
}
