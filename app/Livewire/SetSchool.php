<?php

namespace App\Livewire;

use App\Services\School\SchoolService;
use Livewire\Component;

class SetSchool extends Component
{
    public $schools;

    public function mount(SchoolService $schoolService)
    {
        $this->schools = $schoolService->getAllSchools();
    }

    public function render()
    {
        return view('livewire.set-school');
    }
}
