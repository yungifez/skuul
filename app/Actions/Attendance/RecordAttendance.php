<?php

namespace App\Actions\Attendance;

use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\AttendanceChange;
use App\Models\AttendanceRecord;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
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

        $this->failIfRecordsDoNotFit($enrollment, $day, $kind, $subject);

        return DB::transaction(function () use ($enrollment, $status, $day, $kind, $subject, $actor, $reason, $source): AttendanceRecord {
            $record = AttendanceRecord::firstOrNew([
                'student_record_id' => $enrollment->id,
                'attended_on' => $day->toDateString(),
                'kind' => $kind->value,
                'subject_id' => $subject?->id,
            ]);

            $previous = $record->exists ? $record->status : null;

            $record->fill([
                'school_id' => $enrollment->school_id ?? current_school_id(),
                'academic_year_id' => current_academic_year_id(),
                'semester_id' => current_semester_id(),
                'my_class_id' => $enrollment->my_class_id,
                'section_id' => $enrollment->section_id,
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
    }

    /**
     * Check the day, the student, and the lesson.
     *
     * @throws InvalidValueException
     * @throws ClosedPeriodException
     */
    private function failIfRecordsDoNotFit(StudentRecord $enrollment, Carbon $day, AttendanceKind $kind, ?Subject $subject): void
    {
        if ($day->isFuture()) {
            throw new InvalidValueException('You cannot take the register for a day that has not happened.');
        }

        if ($enrollment->status->isClosed()) {
            throw new InvalidValueException('This enrollment is closed. It cannot take attendance.');
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

        $period = current_semester() ?? current_academic_year();

        if ($period !== null && $period->isClosed()) {
            throw new ClosedPeriodException('You cannot take attendance in a closed academic period.');
        }
    }
}
