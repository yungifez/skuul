<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\Semester\SemesterService;

class CreateExamForm extends Component
{
    public $semesters;

    public function mount(SemesterService $semesterService)
    {
        $this->semesters = $semesterService->getAllSemestersInAcademicYear(auth()->user()->school->academic_year_id);
    }
    public function render()
    {
        return view('livewire.create-exam-form');
    }
}
