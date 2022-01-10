<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\AcademicYear\AcademicYearService;

class AcademicYearSet extends Component
{
    public function mount(AcademicYearService $academicYearService)
    {
        $this->academicYears = $academicYearService->getAllAcademicYears();
    }

    public function render()
    {
        return view('livewire.academic-year-set');
    }
}
