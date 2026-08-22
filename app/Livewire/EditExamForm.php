<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Services\AcademicPeriod\AcademicPeriodService;
use Livewire\Component;

class EditExamForm extends Component
{
    public Exam $exam;

    public $academicPeriods;

    public function mount(AcademicPeriodService $academicPeriodService)
    {
        $this->academicPeriods = $academicPeriodService->getAllAcademicPeriodsInAcademicYear(current_academic_year_id());
    }

    public function render()
    {
        return view('livewire.edit-exam-form');
    }
}
