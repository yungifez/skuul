<?php

namespace App\Livewire;

use App\Services\AcademicPeriod\AcademicPeriodService;
use Livewire\Component;

class SetAcademicPeriod extends Component
{
    public $academicPeriods;

    public function mount(AcademicPeriodService $academicPeriodService)
    {
        $this->academicPeriods = $academicPeriodService->getAllAcademicPeriodsInAcademicYear(current_academic_year()->id);
    }

    public function render()
    {
        return view('livewire.set-academic-period');
    }
}
