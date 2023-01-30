<?php

namespace App\Http\Livewire;

use App\Services\Semester\SemesterService;
use Livewire\Component;

class ListSemestersTable extends Component
{
    public function mount(SemesterService $semesterService)
    {
        $this->academicYear = auth()->user()->school->academicYear;
        $this->semesters = $semesterService->getAllSemestersInAcademicYear($this->academicYear->id);
    }

    public function render()
    {
        return view('livewire.list-semesters-table');
    }
}
