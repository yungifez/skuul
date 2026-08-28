<?php

namespace App\Livewire;

use App\Actions\Curriculum\MoveAcademicCycleSection;
use App\Actions\Curriculum\MoveAcademicLevel;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use Illuminate\View\View;
use Livewire\Component;

class AcademicYearStructureTree extends Component
{
    public AcademicYear $academicYear;

    public function mount(AcademicYear $academicYear): void
    {
        $this->authorize('update', $academicYear);
        $this->academicYear = $academicYear;
    }

    public function moveLevel(int $academicLevelId, string $direction, MoveAcademicLevel $moveAcademicLevel): void
    {
        $academicLevel = AcademicLevel::inSchool($this->academicYear->school_id)->findOrFail($academicLevelId);
        $this->authorize('update', $academicLevel);

        abort_unless(in_array($direction, ['up', 'down'], true), 422);

        $moveAcademicLevel->move($academicLevel, $direction, auth()->user());
    }

    public function moveSection(int $academicCycleSectionId, string $direction, MoveAcademicCycleSection $moveAcademicCycleSection): void
    {
        $section = AcademicCycleSection::inSchool($this->academicYear->school_id)
            ->where('academic_year_id', $this->academicYear->id)
            ->findOrFail($academicCycleSectionId);
        $this->authorize('update', $section);

        abort_unless(in_array($direction, ['up', 'down'], true), 422);

        $moveAcademicCycleSection->move($section, $direction, auth()->user());
    }

    public function render(): View
    {
        $academicYear = AcademicYear::inSchool($this->academicYear->school_id)
            ->with(['cycleSections.academicLevel', 'cycleSections.homeroomTeacher'])
            ->findOrFail($this->academicYear->id);
        $academicLevels = AcademicLevel::inSchool($academicYear->school_id)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'status']);

        return view('livewire.academic-year-structure-tree', compact('academicYear', 'academicLevels'));
    }
}
