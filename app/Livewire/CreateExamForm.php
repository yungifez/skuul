<?php

namespace App\Livewire;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use Livewire\Component;

class CreateExamForm extends Component
{
    public $academicPeriods;

    public ?AcademicYear $academicYear = null;

    public ?int $selectedAcademicPeriodId = null;

    public function mount(): void
    {
        $academicYearId = request()->integer('academic_year_id') ?: current_academic_year_id();
        $this->academicYear = AcademicYear::inSchool()->find($academicYearId);
        $this->academicPeriods = $this->academicYear?->academicPeriods()
            ->get()
            ->filter(fn (AcademicPeriod $period): bool => $period->status->acceptsExamPlanning())
            ->values() ?? collect();

        $requestedPeriodId = request()->integer('academic_period_id');
        $currentPeriodId = current_academic_period_id();

        $selectedPeriod = $this->academicPeriods->first(fn ($period): bool => $period->id === $requestedPeriodId);

        if ($selectedPeriod === null) {
            $selectedPeriod = $this->academicPeriods->first(fn ($period): bool => $period->id === $currentPeriodId);
        }

        if ($selectedPeriod === null) {
            $selectedPeriod = $this->academicPeriods->first();
        }

        if ($selectedPeriod !== null) {
            $this->selectedAcademicPeriodId = $selectedPeriod->id;
        }
    }

    public function render()
    {
        return view('livewire.create-exam-form');
    }
}
