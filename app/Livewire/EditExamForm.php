<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Services\AcademicPeriod\AcademicPeriodService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditExamForm extends Component
{
    public Exam $exam;

    public $academicPeriods;

    public function mount(AcademicPeriodService $academicPeriodService): void
    {
        $this->exam->loadMissing('academicPeriod');

        $academicYearId = $this->exam->academicPeriod?->academic_year_id;

        $this->academicPeriods = $academicYearId === null
            ? collect()
            : $academicPeriodService->getAllAcademicPeriodsInAcademicYear($academicYearId);
    }

    public function render(): View
    {
        return view('livewire.edit-exam-form');
    }
}
