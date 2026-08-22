<?php

namespace App\Livewire;

use App\Actions\Enrollment\MoveEnrollmentBetweenCampuses;
use App\Actions\Enrollment\RequestCampusMove;
use App\Enums\AcademicStructureStatus;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\CampusMoveRequest;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Authorization\CampusMoveAuthority;
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

    /** @var array<int, array{id: int, name: string, level: string, campus: string}> */
    public array $campusCycleSections = [];

    public ?int $campusCycleSectionId = null;

    public string $campusReason = '';

    public string $campusEffectiveOn = '';

    public ?CampusMoveRequest $openCampusMoveRequest = null;

    public bool $movesCampusFreely = false;

    public function mount(): void
    {
        $this->statusEffectiveOn = now()->toDateString();
        $this->placementEffectiveOn = now()->toDateString();
        $this->campusEffectiveOn = now()->toDateString();
        $this->loadCycleSections();
        $this->loadCampusCycleSections();
        $this->refreshEnrollment();
    }

    /**
     * Move the student to another campus, or ask that campus to take them.
     *
     * A person with organization authority moves the student straight away.
     * A campus administrator only asks, and the receiving campus decides.
     */
    public function moveCampus(
        MoveEnrollmentBetweenCampuses $moveEnrollmentBetweenCampuses,
        RequestCampusMove $requestCampusMove,
        CampusMoveAuthority $campusMoveAuthority,
    ): void {
        Gate::authorize('update', [$this->student, 'student']);

        if ($this->studentRecord === null) {
            $this->addError('campusCycleSectionId', 'This person has no enrollment in the current school.');

            return;
        }

        $this->validate([
            'campusCycleSectionId' => ['required', 'integer'],
            'campusReason' => ['nullable', 'string', 'max:1000'],
            'campusEffectiveOn' => ['required', 'date'],
        ]);

        // Only a section of a sibling campus may be chosen, so read it from
        // the organization rather than the working school.
        $academicCycleSection = AcademicCycleSection::query()
            ->with('school')
            ->whereKey($this->campusCycleSectionId)
            ->whereIn('school_id', $this->siblingCampusIds())
            ->first();

        if ($academicCycleSection === null) {
            $this->addError('campusCycleSectionId', 'That section does not belong to a campus of this organization.');

            return;
        }

        $actor = auth()->user();
        $reason = filled($this->campusReason) ? $this->campusReason : null;
        $movesFreely = $campusMoveAuthority->movesFreely($actor, $academicCycleSection->school);

        if (!$movesFreely && !$campusMoveAuthority->canRequest($actor, $this->studentRecord->school)) {
            $this->addError('campusCycleSectionId', 'You cannot move a student to another campus.');

            return;
        }

        try {
            if ($movesFreely) {
                $moveEnrollmentBetweenCampuses->move(
                    enrollment: $this->studentRecord,
                    academicCycleSection: $academicCycleSection,
                    actor: $actor,
                    reason: $reason,
                    effectiveOn: Carbon::parse($this->campusEffectiveOn),
                );
                $message = 'The student now attends the other campus. The enrollment and its history stayed with them.';
            } else {
                $requestCampusMove->request(
                    enrollment: $this->studentRecord,
                    academicCycleSection: $academicCycleSection,
                    actor: $actor,
                    reason: $reason,
                    effectiveOn: Carbon::parse($this->campusEffectiveOn),
                );
                $message = 'The other campus was asked to take this student. The move happens when they agree.';
            }
        } catch (InvalidValueException $exception) {
            $this->addError('campusCycleSectionId', $exception->getMessage());

            return;
        }

        $this->campusReason = '';
        session()->flash('success', $message);
        $this->refreshEnrollment();
    }

    /**
     * Take back a request this campus made.
     */
    public function cancelCampusMove(RequestCampusMove $requestCampusMove, CampusMoveAuthority $campusMoveAuthority): void
    {
        Gate::authorize('update', [$this->student, 'student']);

        if ($this->openCampusMoveRequest === null) {
            return;
        }

        if (!$campusMoveAuthority->canCancel(auth()->user(), $this->openCampusMoveRequest)) {
            $this->addError('campusCycleSectionId', 'You cannot take this request back.');

            return;
        }

        $requestCampusMove->cancel($this->openCampusMoveRequest, auth()->user());

        session()->flash('success', 'The campus move request was taken back.');
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
        $this->openCampusMoveRequest = $this->studentRecord === null
            ? null
            : app(RequestCampusMove::class)->openRequestFor($this->studentRecord)?->load(['toSchool', 'academicCycleSection.academicLevel']);
        $this->movesCampusFreely = $this->studentRecord !== null
            && $this->siblingCampusIds() !== []
            && app(CampusMoveAuthority::class)->movesFreely(auth()->user(), current_school());
    }

    /**
     * Get the ids of the other campuses in this school's organization.
     *
     * @return array<int, int>
     */
    private function siblingCampusIds(): array
    {
        $school = current_school();

        if ($school === null) {
            return [];
        }

        return School::query()
            ->where('organization_id', $school->organization_id)
            ->whereKeyNot($school->id)
            ->pluck('id')
            ->all();
    }

    /**
     * Load the sections a student could move to on another campus.
     */
    private function loadCampusCycleSections(): void
    {
        $campusIds = $this->siblingCampusIds();

        if ($campusIds === []) {
            $this->campusCycleSections = [];

            return;
        }

        $this->campusCycleSections = AcademicCycleSection::query()
            ->with(['academicLevel', 'school'])
            ->whereIn('school_id', $campusIds)
            ->where('status', AcademicStructureStatus::Active)
            ->orderBy('school_id')
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn (AcademicCycleSection $cycleSection): array => [
                'id' => $cycleSection->id,
                'name' => $cycleSection->label ?? $cycleSection->name,
                'level' => $cycleSection->academicLevel->label ?? $cycleSection->academicLevel->name,
                'campus' => $cycleSection->school->name,
            ])
            ->all();
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
