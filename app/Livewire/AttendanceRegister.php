<?php

namespace App\Livewire;

use App\Enums\AttendanceStatus;
use App\Enums\Feature;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Services\Attendance\AttendanceRegister as AttendanceRegisterService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Livewire\Attributes\Url;
use Livewire\Component;

class AttendanceRegister extends Component
{
    #[Url(as: 'academic_cycle_section_id', except: '')]
    public string $academicCycleSectionId = '';

    #[Url(as: 'attended_on')]
    public string $attendedOn = '';

    /** @var array<int, string> */
    public array $statusesByStudent = [];

    public ?string $feedback = null;

    protected AttendanceRegisterService $register;

    public function boot(AttendanceRegisterService $register): void
    {
        abort_unless(features()->enabled(Feature::Attendance), 404);
        Gate::authorize('read attendance');

        $this->register = $register;
    }

    public function mount(): void
    {
        if (!$this->isValidDate($this->attendedOn)) {
            $this->attendedOn = now()->toDateString();
        }

        $section = $this->selectedSection();

        if ($section !== null) {
            $this->loadStatuses($section);
        }
    }

    public function updatedAcademicCycleSectionId(): void
    {
        $this->resetValidation();
        $this->feedback = null;
        $this->statusesByStudent = [];

        if ($section = $this->selectedSection()) {
            $this->loadStatuses($section);
        }
    }

    public function updatedAttendedOn(): void
    {
        $this->resetValidation();
        $this->feedback = null;

        if (!$this->isValidDate($this->attendedOn)) {
            $this->addError('attendedOn', 'Choose a valid day.');

            return;
        }

        $this->statusesByStudent = [];

        if ($section = $this->selectedSection()) {
            $this->loadStatuses($section);
        }
    }

    public function moveDay(int $offset): void
    {
        if (!in_array($offset, [-1, 1], true) || !$this->isValidDate($this->attendedOn)) {
            return;
        }

        $this->attendedOn = Carbon::parse($this->attendedOn)->addDays($offset)->toDateString();
        $this->updatedAttendedOn();
    }

    public function goToToday(): void
    {
        $this->attendedOn = now()->toDateString();
        $this->updatedAttendedOn();
    }

    public function markAll(string $status): void
    {
        Gate::authorize('take attendance');
        $status = AttendanceStatus::tryFrom($status)?->value;

        if ($status === null) {
            $this->addError('statusesByStudent', 'Choose a valid attendance status.');

            return;
        }

        $section = $this->selectedSection();

        if ($section === null) {
            return;
        }

        $this->statusesByStudent = array_fill_keys($this->register->students($section)->modelKeys(), $status);
    }

    public function save(): void
    {
        Gate::authorize('take attendance');
        $this->feedback = null;

        $validated = $this->validate([
            'academicCycleSectionId' => ['required', 'integer', Rule::exists((new AcademicCycleSection)->getTable(), 'id')->where('school_id', current_school_id())],
            'attendedOn' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $section = $this->selectedSection();

        if ($section === null) {
            $this->addError('academicCycleSectionId', 'Choose a section in this school.');

            return;
        }

        $students = $this->register->students($section);

        if ($students->isEmpty()) {
            $this->addError('register', 'There are no learners in this section to mark.');

            return;
        }

        $statusRules = [];

        foreach ($students as $student) {
            $statusRules['statusesByStudent.'.$student->id] = ['required', Rule::enum(AttendanceStatus::class)];
        }

        $this->validate($statusRules);

        try {
            $this->register->save(
                students: $students,
                statusesByStudent: $this->statusesByStudent,
                date: Carbon::parse($validated['attendedOn']),
                actor: auth()->user(),
            );
        } catch (InvalidValueException|ClosedPeriodException|InvalidArgumentException $exception) {
            $this->addError('register', $exception->getMessage());

            return;
        }

        $this->feedback = 'Attendance register saved.';
    }

    public function render(): View
    {
        $section = $this->selectedSection();
        $students = $section === null ? new EloquentCollection : $this->register->students($section);
        $recordedStatuses = $section === null || !$this->isValidDate($this->attendedOn)
            ? []
            : $this->register->statuses($section, $students, Carbon::parse($this->attendedOn));

        foreach ($students as $student) {
            $this->statusesByStudent[$student->id] ??= isset($recordedStatuses[$student->id])
                ? $recordedStatuses[$student->id]->value
                : AttendanceStatus::Present->value;
        }

        return ViewFactory::make('livewire.attendance-register', [
            'sections' => $this->register->sections(),
            'section' => $section,
            'students' => $students,
            'statuses' => AttendanceStatus::cases(),
            'canTakeAttendance' => auth()->user()?->can('take attendance') === true,
        ]);
    }

    private function selectedSection(): ?AcademicCycleSection
    {
        if (!ctype_digit($this->academicCycleSectionId) || (int) $this->academicCycleSectionId < 1) {
            return null;
        }

        return $this->register->section((int) $this->academicCycleSectionId);
    }

    private function loadStatuses(AcademicCycleSection $section): void
    {
        $students = $this->register->students($section);
        $recordedStatuses = $this->register->statuses($section, $students, Carbon::parse($this->attendedOn));
        $this->statusesByStudent = [];

        foreach ($students as $student) {
            $this->statusesByStudent[$student->id] = isset($recordedStatuses[$student->id])
                ? $recordedStatuses[$student->id]->value
                : AttendanceStatus::Present->value;
        }
    }

    private function isValidDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1
            && Carbon::hasFormat($date, 'Y-m-d')
            && Carbon::parse($date)->toDateString() === $date;
    }
}
