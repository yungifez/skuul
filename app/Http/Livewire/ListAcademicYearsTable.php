<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\AcademicYear\AcademicYearService;

class ListAcademicYearsTable extends Component
{
  
   //mount method
    public function mount(AcademicYearService $academicYearService)
    {
        $this->academicYears = $academicYearService->getAllAcademicYears();
    }
   
    public function render()
    {
        return view('livewire.list-academic-years-table');
    }
}
