<?php

namespace App\Livewire;

use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\Carbon;
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

    public array $cycleSections = [];

    public ?int $placementCycleSectionId = null;

    public string $placementReason = '';

    public string $placementEffectiveOn = '';

    public function mount(): void
    {
        $this->statusEffectiveOn = now()->toDateString();
        $this->placementEffectiveOn = now()->toDateString();
        $this->loadCycleSections();
        $this->refreshEnrollment();
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
            $this->addError('placementCycleSectionId', 'This person has no enrollment in the current school.');

            return;
        }

        $this->validate([
            'placementCycleSectionId' => ['required', 'integer'],
            'placementReason' => ['nullable', 'string', 'max:1000'],
            'placementEffectiveOn' => ['required', 'date'],
        ]);

        $academicCycleSection = AcademicCycleSection::inSchool()
            ->whereKey($this->placementCycleSectionId)
            ->where('academic_year_id', current_academic_year_id())
            ->firstOrFail();

        try {
            $changeEnrollmentPlacement->place(
                enrollment: $this->studentRecord,
                academicCycleSection: $academicCycleSection,
                academicPeriod: current_academic_period(),
                actor: auth()->user(),
                reason: filled($this->placementReason) ? $this->placementReason : null,
                effectiveOn: Carbon::parse($this->placementEffectiveOn),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('placementCycleSectionId', $exception->getMessage());

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
            'academicPeriod' => current_academic_period(),
            'canManageEnrollment' => auth()->user()->can('update', [$this->student, 'student']),
        ]);
    }

    private function refreshEnrollment(): void
    {
        $this->student = $this->student->loadMissing([
            'studentRecord.placements.academicYear',
            'studentRecord.placements.academicCycleSection.academicLevel',
        ]);
        $this->studentRecord = $this->student->studentRecord()
            ->with([
                'academicCycleSection.academicLevel',
                'school',
                'statusChanges.changedBy',
                'placements.academicYear',
                'placements.academicPeriod',
                'placements.academicCycleSection.academicLevel',
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
        $this->placementCycleSectionId = $this->studentRecord?->academic_cycle_section_id;
    }

    private function loadCycleSections(): void
    {
        $this->cycleSections = AcademicCycleSection::inSchool()
            ->with('academicLevel')
            ->where('academic_year_id', current_academic_year_id())
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn (AcademicCycleSection $cycleSection): array => [
                'id' => $cycleSection->id,
                'name' => $cycleSection->label ?? $cycleSection->name,
                'level' => $cycleSection->academicLevel->label ?? $cycleSection->academicLevel->name,
            ])
            ->values()
            ->all();
    }
}
