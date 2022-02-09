<?php

namespace App\Http\Livewire;

use App\Services\AcademicYear\AcademicYearService;
use Livewire\Component;

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
