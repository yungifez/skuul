<?php

namespace App\Livewire;

use App\Services\AcademicYear\AcademicYearService;
use Livewire\Component;

class SetAcademicYear extends Component
{
    public $academicYears;

    public function mount(AcademicYearService $academicYearService)
    {
        $this->academicYears = $academicYearService->getAllAcademicYears();
    }

    public function render()
    {
        return view('livewire.set-academic-year');
    }
}
