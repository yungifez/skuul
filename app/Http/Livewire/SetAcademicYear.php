<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\AcademicYear\AcademicYearService;

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
