<?php

namespace App\Livewire;

use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ShowStudentProfile extends Component
{
    public User $student;

    public ?StudentRecord $studentRecord = null;

    public array $statusOptions = [];

    public string $statusSelection = '';

    public string $statusReason = '';

    public string $statusEffectiveOn = '';

    public array $classes = [];

    public array $sections = [];

    public ?int $placementClassId = null;

    public ?int $placementSectionId = null;

    public string $placementReason = '';

    public string $placementEffectiveOn = '';

    public function mount(): void
    {
        $this->statusEffectiveOn = now()->toDateString();
        $this->placementEffectiveOn = now()->toDateString();
        $this->loadClasses();
        $this->refreshEnrollment();
    }

    public function updatedPlacementClassId(): void
    {
        $this->placementSectionId = null;
        $this->sections = $this->classSections($this->placementClassId);
    }

    public function changeStatus(ChangeEnrollmentStatus $changeEnrollmentStatus): void
    {
        Gate::authorize('update', [$this->student, 'student']);

        if ($this->studentRecord === null) {
            $this->addError('statusSelection', 'This person has no enrollment in the current school.');

            return;
        }

        $this->validate([
            'statusSelection' => ['required', 'in:'.implode(',', array_column($this->statusOptions, 'value'))],
            'statusReason' => ['nullable', 'string', 'max:1000'],
            'statusEffectiveOn' => ['required', 'date'],
        ]);

        try {
            $changeEnrollmentStatus->change(
                enrollment: $this->studentRecord,
                status: EnrollmentStatus::from($this->statusSelection),
                actor: auth()->user(),
                reason: filled($this->statusReason) ? $this->statusReason : null,
                effectiveOn: Carbon::parse($this->statusEffectiveOn),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('statusSelection', $exception->getMessage());

            return;
        }

        $this->statusReason = '';
        session()->flash('success', 'Enrollment status updated.');
        $this->refreshEnrollment();
    }

    public function changePlacement(ChangeEnrollmentPlacement $changeEnrollmentPlacement): void
    {
        Gate::authorize('update', [$this->student, 'student']);

        if ($this->studentRecord === null) {
            $this->addError('placementClassId', 'This person has no enrollment in the current school.');

            return;
        }

        $this->validate([
            'placementClassId' => ['required', 'integer'],
            'placementSectionId' => ['nullable', 'integer'],
            'placementReason' => ['nullable', 'string', 'max:1000'],
            'placementEffectiveOn' => ['required', 'date'],
        ]);

        $class = MyClass::query()
            ->whereKey($this->placementClassId)
            ->whereHas('classGroup', fn (Builder $query): Builder => $query->where('school_id', current_school_id()))
            ->firstOrFail();

        $section = $this->placementSectionId === null
            ? null
            : Section::query()->whereKey($this->placementSectionId)->whereBelongsTo($class, 'myClass')->firstOrFail();

        try {
            $changeEnrollmentPlacement->place(
                enrollment: $this->studentRecord,
                class: $class,
                section: $section,
                academicYear: current_academic_year(),
                semester: current_semester(),
                actor: auth()->user(),
                reason: filled($this->placementReason) ? $this->placementReason : null,
                effectiveOn: Carbon::parse($this->placementEffectiveOn),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('placementClassId', $exception->getMessage());

            return;
        }

        $this->placementReason = '';
        session()->flash('success', 'Enrollment placement updated.');
        $this->refreshEnrollment();
    }

    public function render()
    {
        return view('livewire.show-student-profile', [
            'academicYear' => current_academic_year(),
            'semester' => current_semester(),
            'canManageEnrollment' => auth()->user()->can('update', [$this->student, 'student']),
        ]);
    }

    private function refreshEnrollment(): void
    {
        $this->student = $this->student->loadMissing([
            'studentRecord.placements.academicYear',
            'studentRecord.placements.myClass',
            'studentRecord.placements.section',
        ]);
        $this->studentRecord = $this->student->studentRecord()
            ->with([
                'myClass.classGroup',
                'section',
                'school',
                'statusChanges.changedBy',
                'placements.academicYear',
                'placements.semester',
                'placements.myClass',
                'placements.section',
                'placements.changedBy',
            ])
            ->first();

        $this->statusOptions = $this->studentRecord === null
            ? []
            : collect([$this->studentRecord->status, ...$this->studentRecord->status->allowedNext()])
                ->unique()
                ->map(fn (EnrollmentStatus $status): array => [
                    'value' => $status->value,
                    'label' => $status->label(),
                ])
                ->values()
                ->all();

        $this->statusSelection = $this->studentRecord?->status->value ?? '';
        $this->placementClassId = $this->studentRecord?->my_class_id;
        $this->placementSectionId = $this->studentRecord?->section_id;
        $this->sections = $this->classSections($this->placementClassId);
    }

    private function loadClasses(): void
    {
        $this->classes = MyClass::query()
            ->whereHas('classGroup', fn (Builder $query): Builder => $query->where('school_id', current_school_id()))
            ->with('sections')
            ->orderBy('name')
            ->get()
            ->map(fn (MyClass $class): array => [
                'id' => $class->id,
                'name' => $class->name,
                'sections' => $class->sections->map(fn (Section $section): array => [
                    'id' => $section->id,
                    'name' => $section->name,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }

    private function classSections(?int $classId): array
    {
        if ($classId === null) {
            return [];
        }

        $class = collect($this->classes)->firstWhere('id', $classId);

        return $class['sections'] ?? [];
    }
}
