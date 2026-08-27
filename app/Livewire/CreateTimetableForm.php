<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use Illuminate\View\View;
use Livewire\Component;

class CreateTimetableForm extends Component
{
    /** @var array<int, array{id: int, label: string}> */
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
                'label' => $cycleSection->academicLevel->name
                    .' · '.($cycleSection->label ?? $cycleSection->name),
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.create-timetable-form');
    }
}
