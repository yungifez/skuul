<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Enums\Role;
use App\Models\AcademicCycleSection;
use App\Models\Timetable;
use Illuminate\View\View;
use Livewire\Component;

class ListTimetablesTable extends Component
{
    /** @var array<int, array{id: int, label: string}> */
    public array $cycleSections = [];

    /** @var array<int, array{id: int, name: string, description: string|null, status: string, published_at: string|null}> */
    public array $timetables = [];

    public ?int $academicCycleSectionId = null;

    public bool $isStudent = false;

    public function mount(): void
    {
        $this->isStudent = auth()->user()->hasRole(Role::Student);

        if ($this->isStudent) {
            $this->academicCycleSectionId = auth()->user()->studentRecord?->academic_cycle_section_id;
        } else {
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

            $this->academicCycleSectionId = $this->cycleSections[0]['id'] ?? null;
        }

        $this->loadTimetables();
    }

    public function updatedAcademicCycleSectionId(): void
    {
        if ($this->isStudent) {
            return;
        }

        if (!in_array($this->academicCycleSectionId, array_column($this->cycleSections, 'id'), true)) {
            $this->academicCycleSectionId = $this->cycleSections[0]['id'] ?? null;
        }

        $this->loadTimetables();
    }

    private function loadTimetables(): void
    {
        if ($this->academicCycleSectionId === null || current_academic_period_id() === null) {
            $this->timetables = [];

            return;
        }

        $this->timetables = Timetable::query()
            ->where('academic_period_id', current_academic_period_id())
            ->where('academic_cycle_section_id', $this->academicCycleSectionId)
            ->orderByDesc('published_at')
            ->orderByDesc('revision')
            ->get()
            ->map(fn (Timetable $timetable): array => [
                'id' => $timetable->id,
                'name' => $timetable->name,
                'description' => $timetable->description,
                'status' => $timetable->status->label(),
                'published_at' => $timetable->published_at?->toFormattedDateString(),
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.list-timetables-table');
    }
}
