<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use App\Models\ParentRecord;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class AssignStudentsToParent extends Component
{
    public User $parent;

    /** @var array<int, array{id: int, label: string}> */
    public array $cycleSections = [];

    public ?int $academicCycleSectionId = null;

    /** @var array<int, array{id: int, name: string, admission_number: string|null}> */
    public array $students = [];

    public ?int $studentId = null;

    /** @var array<int, array{id: int, name: string, email: string, admission_number: string|null, cycle_section: string|null}> */
    public array $children = [];

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
                'label' => ($cycleSection->academicLevel->label ?? $cycleSection->academicLevel->name)
                    .' · '.($cycleSection->label ?? $cycleSection->name),
            ])
            ->all();

        $this->loadChildren();

        if ($this->cycleSections === []) {
            return;
        }

        $this->academicCycleSectionId = $this->cycleSections[0]['id'];
        $this->loadStudents();
    }

    public function updatedAcademicCycleSectionId(): void
    {
        $this->loadStudents();
    }

    private function loadStudents(): void
    {
        if ($this->academicCycleSectionId === null) {
            $this->students = [];
            $this->studentId = null;

            return;
        }

        $this->students = User::role('student')
            ->ofSchool()
            ->whereHas('studentRecord', fn ($query) => $query->where('academic_cycle_section_id', $this->academicCycleSectionId))
            ->with('studentRecord:id,user_id,admission_number')
            ->orderBy('name')
            ->get()
            ->map(fn (User $student): array => [
                'id' => $student->id,
                'name' => $student->name,
                'admission_number' => $student->studentRecord?->admission_number,
            ])
            ->all();

        $this->studentId = $this->students[0]['id'] ?? null;
    }

    private function loadChildren(): void
    {
        $parentRecord = ParentRecord::query()->firstWhere('user_id', $this->parent->id);

        if ($parentRecord === null) {
            $this->children = [];

            return;
        }

        $this->children = $parentRecord->students()
            ->with('studentRecord.academicCycleSection.academicLevel')
            ->orderBy('name')
            ->get()
            ->map(fn (User $student): array => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'admission_number' => $student->studentRecord?->admission_number,
                'cycle_section' => $student->studentRecord?->academicCycleSection === null
                    ? null
                    : ($student->studentRecord->academicCycleSection->academicLevel->label
                        ?? $student->studentRecord->academicCycleSection->academicLevel->name)
                        .' · '.($student->studentRecord->academicCycleSection->label
                            ?? $student->studentRecord->academicCycleSection->name),
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.assign-students-to-parent');
    }
}
