<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\Semester\SemesterService;

class SemesterSet extends Component
{
    public $semesters;

    public function mount(SemesterService $semesterService)
    {
        $this->semesters = $semesterService->getAllSemestersInAcademicYear(auth()->user()->school->academicYear->id);
    }
    public function render()
    {
        return view('livewire.semester-set');
    }
}
