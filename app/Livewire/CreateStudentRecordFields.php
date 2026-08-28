<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use Illuminate\View\View;
use Livewire\Component;

class CreateStudentRecordFields extends Component
{
    /** @var array<int, array{id: int, level: string, name: string}> */
    public array $cycleSections = [];

    public function mount(): void
    {
        $this->cycleSections = AcademicCycleSection::inSchool()
            ->with('academicLevel')
            ->where('academic_year_id', current_academic_year_id())
            ->where('status', AcademicStructureStatus::Active)
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn (AcademicCycleSection $cycleSection): array => [
                'id' => $cycleSection->id,
                'level' => $cycleSection->academicLevel->name,
                'name' => $cycleSection->label ?? $cycleSection->name,
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.create-student-record-fields');
    }
}
