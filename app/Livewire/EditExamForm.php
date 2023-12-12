<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Services\Semester\SemesterService;
use Livewire\Component;

class EditExamForm extends Component
{
    public Exam $exam;

    public $semesters;

    public function mount(SemesterService $semesterService)
    {
        $this->semesters = $semesterService->getAllSemestersInAcademicYear(auth()->user()->school->academic_year_id);
    }

    public function render()
    {
        return view('livewire.edit-exam-form');
    }
}
