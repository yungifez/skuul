<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;
use App\Services\Semester\SemesterService;

class EditExamForm extends Component
{
    public Exam $exam;

    public function mount(SemesterService $semesterService)
    {
        $this->semesters = $semesterService->getAllSemestersInAcademicYear(auth()->user()->school->academic_year_id);
    }
    public function render()
    {
        return view('livewire.edit-exam-form');
    }
}
