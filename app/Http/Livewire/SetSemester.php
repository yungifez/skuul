<?php

namespace App\Http\Livewire;

use App\Services\Semester\SemesterService;
use Livewire\Component;

class SetSemester extends Component
{
    public $semesters;

    public function mount(SemesterService $semesterService)
    {
        $this->semesters = $semesterService->getAllSemestersInAcademicYear(auth()->user()->school->academicYear->id);
    }

    public function render()
    {
        return view('livewire.set-semester');
    }
}
