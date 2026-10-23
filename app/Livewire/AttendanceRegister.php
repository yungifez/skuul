<?php

namespace App\Livewire;

use App\Enums\AttendanceStatus;
use App\Enums\Feature;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\TimetableTimeSlot;
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

/**
 * Take a register: the daily one of a section, or the one of a lesson.
 *
 * A campus where learners move between rooms takes a register in each
 * lesson. Its list is the offering's roster, and only the offering's
 * teachers and curriculum managers take it.
 */
class AttendanceRegister extends Component
{
    #[Url(as: 'register', except: 'daily')]
    public string $registerType = 'daily';

    #[Url(as: 'academic_cycle_section_id', except: '')]
    public string $academicCycleSectionId = '';

    #[Url(as: 'course_offering_id', except: '')]
    public string $courseOfferingId = '';

    #[Url(as: 'time_slot_id', except: '')]
    public string $timeSlotId = '';

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
            $this->attendedOn = school_today()->toDateString();
        }

        if (!in_array($this->registerType, ['daily', 'lesson'], true)) {
            $this->registerType = 'daily';
        }

        $this->loadStatuses();
    }

    public function updatedRegisterType(): void
    {
        if (!in_array($this->registerType, ['daily', 'lesson'], true)) {
            $this->registerType = 'daily';
        }

        $this->reopen();
    }

    public function updatedAcademicCycleSectionId(): void
    {
        $this->reopen();
    }

    public function updatedCourseOfferingId(): void
    {
        $this->timeSlotId = '';
        $this->reopen();
    }

    public function updatedTimeSlotId(): void
    {
        $this->reopen();
    }

    /**
     * Open the chosen register afresh.
     */
    private function reopen(): void
    {
        $this->resetValidation();
        $this->feedback = null;
        $this->statusesByStudent = [];
        $this->loadStatuses();
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
        $this->loadStatuses();
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
        $this->attendedOn = school_today()->toDateString();
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

        if (!$this->canTake()) {
            return;
        }

        $this->statusesByStudent = array_fill_keys($this->openStudents()->modelKeys(), $status);
    }

    public function save(): void
    {
        Gate::authorize('take attendance');
        $this->feedback = null;

        if ($this->registerType === 'lesson') {
            $this->saveLesson();

            return;
        }

        $validated = $this->validate([
            'academicCycleSectionId' => ['required', 'integer', Rule::exists((new AcademicCycleSection)->getTable(), 'id')->where('school_id', current_school_id())],
            'attendedOn' => ['required', 'date', 'before_or_equal:'.school_today()->toDateString()],
        ], [
            'attendedOn.before_or_equal' => 'You cannot take attendance for a future day. Choose today or an earlier day.',
        ]);

        $section = $this->selectedSection();

        if ($section === null) {
            $this->addError('academicCycleSectionId', 'Choose a section in this school.');

            return;
        }

        $students = $this->register->students($section, Carbon::parse($validated['attendedOn']));

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

    /**
     * Save the register of one lesson.
     */
    private function saveLesson(): void
    {
        $validated = $this->validate([
            'attendedOn' => ['required', 'date', 'before_or_equal:'.school_today()->toDateString()],
        ], [
            'attendedOn.before_or_equal' => 'You cannot take attendance for a future day. Choose today or an earlier day.',
        ]);
        $offering = $this->selectedOffering();

        if ($offering === null) {
            $this->addError('courseOfferingId', 'Choose a subject you teach.');

            return;
        }

        Gate::authorize('takeLessonRegister', $offering);

        $day = Carbon::parse($validated['attendedOn']);
        $timeSlot = $this->selectedTimeSlot($offering, $day);

        if ($this->timeSlotId !== '' && $timeSlot === null) {
            $this->addError('timeSlotId', 'Choose a lesson time of this subject on that day.');

            return;
        }

        $students = $this->register->lessonStudents($offering);

        if ($students->isEmpty()) {
            $this->addError('register', 'Nobody takes this subject yet.');

            return;
        }

        $statusRules = [];

        foreach ($students as $student) {
            $statusRules['statusesByStudent.'.$student->id] = ['required', Rule::enum(AttendanceStatus::class)];
        }

        $this->validate($statusRules);

        try {
            $this->register->saveLesson($offering, $timeSlot, $students, $this->statusesByStudent, $day, auth()->user());
        } catch (InvalidValueException|ClosedPeriodException|InvalidArgumentException $exception) {
            $this->addError('register', $exception->getMessage());

            return;
        }

        $this->feedback = 'Lesson register saved.';
    }

    public function render(): View
    {
        $section = $this->registerType === 'daily' ? $this->selectedSection() : null;
        $offering = $this->registerType === 'lesson' ? $this->selectedOffering() : null;
        $day = $this->day();
        $students = $this->openStudents();
        $recordedStatuses = $this->recordedStatuses($students);

        foreach ($students as $student) {
            $this->statusesByStudent[$student->id] ??= isset($recordedStatuses[$student->id])
                ? $recordedStatuses[$student->id]->value
                : AttendanceStatus::Present->value;
        }

        $lessonsEnabled = features()->config(Feature::Attendance, 'lesson_register', true);

        return ViewFactory::make('livewire.attendance-register', [
            'sections' => $this->register->sections($section?->id),
            'section' => $section,
            'lessonsEnabled' => $lessonsEnabled,
            'offerings' => $lessonsEnabled && $day !== null && auth()->user() !== null ? $this->register->lessonOfferings(auth()->user(), $day) : new EloquentCollection,
            'offering' => $offering,
            'lessonTimes' => $offering === null || $day === null ? new EloquentCollection : $this->register->lessonTimes($offering, $day),
            'isTodayOrLater' => $day?->greaterThanOrEqualTo(school_today()) === true,
            'students' => $students,
            'closure' => $section === null || $day === null ? null : $this->register->closure($section, $day),
            'statuses' => AttendanceStatus::cases(),
            'canTakeAttendance' => $this->canTake(),
        ]);
    }

    /**
     * Check if the signed-in person may take the register that is open.
     */
    private function canTake(): bool
    {
        if (auth()->user()?->can('take attendance') !== true) {
            return false;
        }

        if ($this->registerType !== 'lesson') {
            return true;
        }

        $offering = $this->selectedOffering();

        return $offering !== null && auth()->user()->can('takeLessonRegister', $offering);
    }

    /**
     * Get the learners of the register that is open.
     *
     * @return EloquentCollection<int, StudentRecord>
     */
    private function openStudents(): EloquentCollection
    {
        if ($this->registerType === 'lesson') {
            $offering = $this->selectedOffering();

            return $offering === null || !$this->canSeeLesson($offering) ? new EloquentCollection : $this->register->lessonStudents($offering);
        }

        $section = $this->selectedSection();

        return $section === null ? new EloquentCollection : $this->register->students($section, $this->day());
    }

    /**
     * @param  EloquentCollection<int, StudentRecord>  $students
     * @return array<int, AttendanceStatus>
     */
    private function recordedStatuses(EloquentCollection $students): array
    {
        $day = $this->day();

        if ($day === null) {
            return [];
        }

        if ($this->registerType === 'lesson') {
            $offering = $this->selectedOffering();

            return $offering === null ? [] : $this->register->lessonStatuses($offering, $this->selectedTimeSlot($offering, $day), $students, $day);
        }

        $section = $this->selectedSection();

        return $section === null ? [] : $this->register->statuses($section, $students, $day);
    }

    /**
     * A lesson register opens for the people who may take it.
     */
    private function canSeeLesson(CourseOffering $offering): bool
    {
        return auth()->user()?->can('takeLessonRegister', $offering) === true;
    }

    private function selectedOffering(): ?CourseOffering
    {
        if (!ctype_digit($this->courseOfferingId) || (int) $this->courseOfferingId < 1) {
            return null;
        }

        return $this->register->lessonOffering((int) $this->courseOfferingId);
    }

    private function selectedTimeSlot(CourseOffering $offering, Carbon $day): ?TimetableTimeSlot
    {
        if (!ctype_digit($this->timeSlotId)) {
            return null;
        }

        return $this->register->lessonTimes($offering, $day)->firstWhere('id', (int) $this->timeSlotId);
    }

    private function selectedSection(): ?AcademicCycleSection
    {
        if (!ctype_digit($this->academicCycleSectionId) || (int) $this->academicCycleSectionId < 1) {
            return null;
        }

        return $this->register->section((int) $this->academicCycleSectionId);
    }

    private function loadStatuses(): void
    {
        $students = $this->openStudents();
        $recordedStatuses = $this->recordedStatuses($students);
        $this->statusesByStudent = [];

        foreach ($students as $student) {
            $this->statusesByStudent[$student->id] = isset($recordedStatuses[$student->id])
                ? $recordedStatuses[$student->id]->value
                : AttendanceStatus::Present->value;
        }
    }

    /**
     * Get the day the register is open for, or null while it is not a date.
     */
    private function day(): ?Carbon
    {
        return $this->isValidDate($this->attendedOn) ? Carbon::parse($this->attendedOn) : null;
    }

    private function isValidDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1
            && Carbon::hasFormat($date, 'Y-m-d')
            && Carbon::parse($date)->toDateString() === $date;
    }
}
