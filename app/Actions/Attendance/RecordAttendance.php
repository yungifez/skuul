<?php

namespace App\Actions\Attendance;

use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Enums\Feature;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\AttendanceChange;
use App\Models\AttendanceRecord;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Services\Calendar\SchoolCalendar;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Take the register for one student.
 *
 * Taking it twice replaces the answer and writes the change to the history
 * beside the record, so a correction is visible instead of silent.
 */
class RecordAttendance
{
    public function __construct(private SchoolCalendar $calendar) {}

    /**
     * Record where the student was.
     *
     * @throws InvalidValueException when the day or the student does not fit
     * @throws ClosedPeriodException when the academic period is closed
     */
    public function record(
        StudentRecord $enrollment,
        AttendanceStatus $status,
        CarbonInterface|string|null $date = null,
        AttendanceKind $kind = AttendanceKind::Daily,
        ?Subject $subject = null,
        ?User $actor = null,
        ?string $reason = null,
        string $source = 'teacher',
    ): AttendanceRecord {
        $day = Carbon::parse($date ?? now())->startOfDay();

        $term = $this->termOf($enrollment, $day);
        $this->failIfRecordsDoNotFit($enrollment, $day, $kind, $subject, $term);
        $section = $this->sectionOn($enrollment, $day);

        $closure = $this->calendar->closureOn($section->school_id, $section->id, $day);

        if ($closure !== null) {
            throw new InvalidValueException("The school was shut on {$day->format('j M Y')} for {$closure->title}. That day has no register.");
        }

        return DB::transaction(function () use ($enrollment, $status, $day, $kind, $subject, $actor, $reason, $source, $term, $section): AttendanceRecord {
            $record = AttendanceRecord::query()
                ->lockForUpdate()
                ->firstOrNew([
                    'student_record_id' => $enrollment->id,
                    'attended_on' => $day->toDateString(),
                    'kind' => $kind->value,
                    'subject_id' => $subject?->id,
                ]);

            // A learner who moved keeps one enrollment. The day they spent at
            // the old campus stays in that campus's register.
            if ($record->exists && $enrollment->school_id !== null && $record->school_id !== $enrollment->school_id) {
                $record->loadMissing('school:id,name');

                throw new InvalidValueException("{$day->format('j M Y')} is in the register of {$record->school?->name}. Only that campus can change it.");
            }

            $previous = $record->exists ? $record->status : null;

            $record->fill([
                'school_id' => $enrollment->school_id ?? current_school_id(),
                'academic_year_id' => $term->academic_year_id ?? current_academic_year_id(),
                'academic_period_id' => $term->id ?? current_academic_period_id(),
                'academic_cycle_section_id' => $section->id,
                'status' => $status,
                'reason' => $reason,
                'source' => $source,
                'recorded_by' => $actor === null ? auth()->id() : $actor->id,
                'recorded_at' => now(),
            ]);

            $record->save();

            // A first answer is not a correction. Only a change is.
            if ($previous !== null && $previous !== $status) {
                AttendanceChange::create([
                    'attendance_record_id' => $record->id,
                    'from_status' => $previous,
                    'to_status' => $status,
                    'reason' => $reason,
                    'changed_by' => $actor === null ? auth()->id() : $actor->id,
                ]);
            }

            return $record;
        });
    }

    /**
     * Take the register for a whole list at once.
     *
     * @param  array<int, array{enrollment: StudentRecord, status: AttendanceStatus, reason?: string|null}>  $entries
     * @return array<int, AttendanceRecord>
     */
    public function recordMany(
        array $entries,
        CarbonInterface|string|null $date = null,
        AttendanceKind $kind = AttendanceKind::Daily,
        ?Subject $subject = null,
        ?User $actor = null,
    ): array {
        // One register is saved whole or not at all.
        return DB::transaction(function () use ($entries, $date, $kind, $subject, $actor): array {
            $records = [];

            foreach ($entries as $entry) {
                $records[] = $this->record(
                    enrollment: $entry['enrollment'],
                    status: $entry['status'],
                    date: $date,
                    kind: $kind,
                    subject: $subject,
                    actor: $actor,
                    reason: $entry['reason'] ?? null,
                );
            }

            return $records;
        });
    }

    /**
     * Check the day, the student, and the lesson.
     *
     * @throws InvalidValueException
     * @throws ClosedPeriodException
     */
    private function failIfRecordsDoNotFit(StudentRecord $enrollment, Carbon $day, AttendanceKind $kind, ?Subject $subject, ?AcademicPeriod $term): void
    {
        $registerKey = $kind === AttendanceKind::Daily ? 'daily_register' : 'lesson_register';

        if (!features()->config(Feature::Attendance, $registerKey, true, $enrollment->school_id)) {
            throw new InvalidValueException("This school has not enabled the {$kind->label()} register.");
        }

        if ($day->isFuture()) {
            throw new InvalidValueException('You cannot take the register for a day that has not happened.');
        }

        if ($enrollment->status->isClosed()) {
            throw new InvalidValueException('This enrollment is closed. It cannot take attendance.');
        }

        if ($enrollment->academic_cycle_section_id === null) {
            throw new InvalidValueException('Place the student in a '.strtolower(school_term('section', 'section')).' before taking attendance.');
        }

        if ($kind === AttendanceKind::Period && $subject === null) {
            throw new InvalidValueException('A lesson register needs the subject it is for.');
        }

        if ($kind === AttendanceKind::Daily && $subject !== null) {
            throw new InvalidValueException('A daily register covers the whole day, not one subject.');
        }

        if ($subject !== null && $enrollment->school_id !== null && $subject->school_id !== $enrollment->school_id) {
            throw new InvalidValueException('The subject belongs to another school.');
        }

        if ($term !== null && ($term->isClosed() || $term->academicYear?->isClosed())) {
            $closedTerm = $term->label ?? $term->name;

            throw new ClosedPeriodException("You cannot take attendance for {$day->format('j M Y')}. {$closedTerm} is closed.");
        }

        $period = current_academic_period() ?? current_academic_year();

        if ($period !== null && $period->isClosed()) {
            throw new ClosedPeriodException('You cannot take attendance in a closed academic period.');
        }
    }

    /**
     * Get the section the learner sat in on the day, at this campus.
     *
     * @throws InvalidValueException when they were at another campus that day
     */
    private function sectionOn(StudentRecord $enrollment, Carbon $day): AcademicCycleSection
    {
        $section = $enrollment->sectionOn($day) ?? $enrollment->academicCycleSection;

        if ($section === null) {
            throw new InvalidValueException('Place the student in a '.strtolower(school_term('section', 'section')).' before taking attendance.');
        }

        if ($enrollment->school_id !== null && $section->school_id !== $enrollment->school_id) {
            $section->loadMissing('school:id,name');

            throw new InvalidValueException("{$enrollment->user?->name} was at {$section->school?->name} on {$day->format('j M Y')}. Only that campus can take its register.");
        }

        return $section;
    }

    /**
     * Get the term the day falls in at the learner's campus.
     *
     * A register taken late belongs to the term of the day it records, not
     * to the term that is open now, so a closed term stays closed.
     */
    private function termOf(StudentRecord $enrollment, Carbon $day): ?AcademicPeriod
    {
        return AcademicPeriod::query()
            ->where('school_id', $enrollment->school_id ?? current_school_id())
            ->topLevel()
            ->covering($day)
            ->with('academicYear')
            ->first();
    }
}
