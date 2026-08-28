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
    public ?AcademicYear $academicYear = null;

    public bool $schoolSetup = false;

    public bool $setupLinks = true;

    public ?string $status = null;

    public function mount(
        ?AcademicYear $academicYear = null,
        bool $schoolSetup = false,
        bool $setupLinks = true,
        bool $allowWithoutAcademicYear = false,
        ?string $status = null,
    ): void {
        if (!$schoolSetup && $academicYear === null && !$allowWithoutAcademicYear) {
            abort(404);
        }

        if (!$schoolSetup && $academicYear !== null) {
            $this->authorize('update', $academicYear);
        }

        $this->academicYear = $academicYear;
        $this->schoolSetup = $schoolSetup;
        $this->setupLinks = $setupLinks;
        $this->status = $status;
    }

    public function moveLevel(int $academicLevelId, string $direction, MoveAcademicLevel $moveAcademicLevel): void
    {
        $academicLevel = AcademicLevel::inSchool($this->workingSchoolId())->findOrFail($academicLevelId);
        $this->authorize('update', $academicLevel);

        abort_unless(in_array($direction, ['up', 'down'], true), 422);

        $moveAcademicLevel->move($academicLevel, $direction, auth()->user());
    }

    public function moveSection(int $academicCycleSectionId, string $direction, MoveAcademicCycleSection $moveAcademicCycleSection): void
    {
        $academicYear = $this->academicYear;
        abort_unless($academicYear !== null, 422, 'Create a school year before adding sections.');

        $section = AcademicCycleSection::inSchool($this->workingSchoolId())
            ->where('academic_year_id', $academicYear->id)
            ->findOrFail($academicCycleSectionId);
        $this->authorize('update', $section);

        abort_unless(in_array($direction, ['up', 'down'], true), 422);

        $moveAcademicCycleSection->move($section, $direction, auth()->user());
    }

    public function render(): View
    {
        $schoolId = $this->workingSchoolId();
        $academicYear = $this->academicYear === null
            ? null
            : AcademicYear::inSchool($schoolId)
                ->with(['cycleSections.academicLevel', 'cycleSections.homeroomTeacher'])
                ->findOrFail($this->academicYear->id);
        $academicLevelsQuery = AcademicLevel::inSchool($schoolId);

        if ($this->status !== null) {
            $academicLevelsQuery->where('status', $this->status);
        }

        $academicLevels = $academicLevelsQuery
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'school_id', 'parent_id', 'name', 'code', 'status', 'is_group']);

        return view('livewire.academic-year-structure-tree', [
            'academicYear' => $academicYear,
            'academicLevels' => $academicLevels,
            'schoolSetup' => $this->schoolSetup,
            'setupLinks' => $this->setupLinks,
        ]);
    }

    private function workingSchoolId(): int
    {
        $schoolId = current_school_id();

        if ($schoolId === null || ($this->academicYear !== null && $this->academicYear->school_id !== $schoolId)) {
            abort(403);
        }

        return $schoolId;
    }
}
