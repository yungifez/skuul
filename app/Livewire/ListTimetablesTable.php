<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Enums\Role;
use App\Enums\TimetableStatus;
use App\Models\AcademicCycleSection;
use App\Models\Timetable;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ListTimetablesTable extends Component
{
    /** @var array<int, array{id: int, label: string}> */
    #[Locked]
    public array $cycleSections = [];

    /** @var array<int, array{id: int, name: string, description: string|null, status: string, variant: string, revision: int, published_at: string|null, can_manage: bool}> */
    #[Locked]
    public array $timetables = [];

    public ?int $academicCycleSectionId = null;

    public string $scope = 'section';

    /**
     * A student sees only the published timetables of the section they attend, and the schoolwide ones.
     */
    #[Locked]
    public bool $isStudent = false;

    public function mount(): void
    {
        Gate::authorize('viewAny', Timetable::class);

        $this->isStudent = auth()->user()->hasRole(Role::Student);

        if ($this->isStudent) {
            $this->academicCycleSectionId = $this->attendedSectionId();
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
                    'label' => $cycleSection->academicLevel->name
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

    public function updatedScope(): void
    {
        if ($this->isStudent || !in_array($this->scope, ['section', 'schoolwide'], true)) {
            $this->scope = 'section';
        }

        $this->loadTimetables();
    }

    private function loadTimetables(): void
    {
        if ($this->isStudent) {
            $this->academicCycleSectionId = $this->attendedSectionId();
        }

        if ((!$this->isStudent && $this->scope === 'section' && $this->academicCycleSectionId === null) || current_academic_period_id() === null) {
            $this->timetables = [];

            return;
        }

        $this->timetables = Timetable::query()
            ->where('academic_period_id', current_academic_period_id())
            ->when($this->isStudent, fn ($query) => $query
                ->where('status', TimetableStatus::Published)
                ->where(fn ($query) => $query
                    ->whereNull('academic_cycle_section_id')
                    ->when($this->academicCycleSectionId !== null, fn ($query) => $query->orWhere('academic_cycle_section_id', $this->academicCycleSectionId))))
            ->when(!$this->isStudent && $this->scope === 'section', fn ($query) => $query->where('academic_cycle_section_id', $this->academicCycleSectionId))
            ->when(!$this->isStudent && $this->scope === 'schoolwide', fn ($query) => $query->whereNull('academic_cycle_section_id'))
            ->orderByDesc('published_at')
            ->orderByDesc('revision')
            ->get()
            ->map(fn (Timetable $timetable): array => [
                'id' => $timetable->id,
                'name' => $timetable->name,
                'description' => $timetable->description,
                'status' => $timetable->status->label(),
                'variant' => match ($timetable->status) {
                    TimetableStatus::Draft => 'secondary',
                    TimetableStatus::Published => 'default',
                    TimetableStatus::Archived => 'outline',
                },
                'revision' => $timetable->revision,
                'published_at' => $timetable->published_at?->toFormattedDateString(),
                // Only a draft accepts changes, so only a draft is built.
                'can_manage' => auth()->user()->can('update', $timetable),
            ])
            ->all();
    }

    private function attendedSectionId(): ?int
    {
        return auth()->user()->studentRecord()->attending()->value('academic_cycle_section_id');
    }

    public function render(): View
    {
        return view('livewire.list-timetables-table');
    }
}
