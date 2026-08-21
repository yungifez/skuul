<?php

namespace App\Livewire;

use App\Services\Semester\SemesterService;
use Livewire\Component;

class CreateExamForm extends Component
{
    public $semesters;

    public function mount(SemesterService $semesterService)
    {
        $this->semesters = $semesterService->getAllSemestersInAcademicYear(current_academic_year_id());
    }

    public function render()
    {
        return view('livewire.create-exam-form');
    }
}
