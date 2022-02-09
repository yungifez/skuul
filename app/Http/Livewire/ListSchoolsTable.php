<?php

namespace App\Http\Livewire;

use App\Services\School\SchoolService;
use Livewire\Component;

class ListSchoolsTable extends Component
{
    public $schools;

    public function mount(SchoolService $schoolService)
    {
        $this->schools = $schoolService->getAllSchools();
    }

    public function render()
    {
        return view('livewire.list-schools-table');
    }
}
