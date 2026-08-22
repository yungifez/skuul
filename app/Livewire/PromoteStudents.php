<?php

namespace App\Livewire;

use App\Models\AcademicCycleSection;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class PromoteStudents extends Component
{
    /** @var array<int, array{id: int, label: string}> */
    public array $cycleSections = [];

    public ?int $sourceAcademicCycleSectionId = null;

    public ?int $destinationAcademicCycleSectionId = null;

    /** @var array<int, array{id: int, name: string, admission_number: string|null}> */
    public array $students = [];

    public function mount(): void
    {
        $this->cycleSections = AcademicCycleSection::inSchool()
            ->with('academicLevel')
            ->where('academic_year_id', current_academic_year_id())
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn (AcademicCycleSection $cycleSection): array => [
                'id'    => $cycleSection->id,
                'label' => ($cycleSection->academicLevel->label ?? $cycleSection->academicLevel->name).' · '.($cycleSection->label ?? $cycleSection->name),
            ])
            ->all();
    }

    public function loadStudents(): void
    {
        $this->validate([
            'sourceAcademicCycleSectionId'      => ['required', 'integer'],
            'destinationAcademicCycleSectionId' => ['required', 'integer', 'different:sourceAcademicCycleSectionId'],
        ]);

        $this->students = User::activeStudents()
            ->whereHas('studentRecord', fn ($query) => $query->where('academic_cycle_section_id', $this->sourceAcademicCycleSectionId))
            ->with('studentRecord:id,user_id,admission_number,academic_cycle_section_id')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $student): array => [
                'id'               => $student->id,
                'name'             => $student->name,
                'admission_number' => $student->studentRecord?->admission_number,
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.promote-students');
    }
}
