<?php

namespace App\Actions\Timetable;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\StaffProfile;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableSubstitution;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use App\Services\Calendar\SchoolCalendar;
use App\Services\Staff\StaffAvailability;
use App\Services\Timetable\TimetableConflictChecker;
use Carbon\CarbonInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class CreateTimetableSubstitution
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private TimetableConflictChecker $conflictChecker,
        private StaffAvailability $availability,
        private SchoolCalendar $calendar,
    ) {}

    public function create(Timetable $timetable, TimetableTimeSlot $slot, int $weekdayId, User $replacementTeacher, CarbonInterface $date, string $reason, User $actor): TimetableSubstitution
    {
        $timetable->loadMissing(['academicCycleSection', 'academicPeriod.academicYear']);
        $weekday = Weekday::query()->find($weekdayId);

        $this->failIfRecordsDoNotFit($timetable, $slot, $weekday, $replacementTeacher, $date);

        try {
            return DB::transaction(function () use ($timetable, $slot, $weekdayId, $replacementTeacher, $date, $reason, $actor): TimetableSubstitution {
                // Holding the teacher keeps two offices from booking the same
                // person into two rooms at once.
                User::query()->whereKey($replacementTeacher->id)->lockForUpdate()->first();

                if (TimetableSubstitution::query()
                    ->where('timetable_time_slot_id', $slot->id)
                    ->where('weekday_id', $weekdayId)
                    ->whereDate('substituted_on', $date)
                    ->exists()) {
                    throw new InvalidValueException('That timetable entry already has a substitution for this date.');
                }

                $this->failIfTheTeacherIsAway($replacementTeacher, $date);
                $this->failIfTheTeacherIsAlreadyCovering($replacementTeacher, $slot, $date);
                $this->failIfTheTeacherHasTheirOwnLesson($replacementTeacher, $timetable, $slot, $date);

                $substitution = TimetableSubstitution::create(['timetable_id' => $timetable->id, 'timetable_time_slot_id' => $slot->id, 'weekday_id' => $weekdayId, 'replacement_teacher_id' => $replacementTeacher->id, 'substituted_on' => $date->toDateString(), 'reason' => $reason, 'approved_by' => $actor->id]);

                $this->auditor->record(AuditAction::TimetableSubstitutionCreated, $substitution, ['timetable_id' => $timetable->id, 'replacement_teacher_id' => $replacementTeacher->id, 'substituted_on' => $date->toDateString()], $actor, $timetable->academicCycleSection->school_id);

                return $substitution;
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('That timetable entry already has a substitution for this date.');
        }
    }

    /**
     * Take back cover that was recorded by mistake, and say so in the audit log.
     *
     * @throws InvalidValueException when the lesson has already happened
     */
    public function withdraw(TimetableSubstitution $substitution, User $actor): void
    {
        if ($substitution->substituted_on->lt(now()->startOfDay())) {
            throw new InvalidValueException('That lesson has already happened, so its cover stays on the record.');
        }

        $substitution->loadMissing('timetable.academicCycleSection');

        DB::transaction(function () use ($substitution, $actor): void {
            $this->auditor->record(
                AuditAction::TimetableSubstitutionWithdrawn,
                $substitution,
                ['timetable_id' => $substitution->timetable_id, 'replacement_teacher_id' => $substitution->replacement_teacher_id, 'substituted_on' => $substitution->substituted_on->toDateString()],
                $actor,
                $substitution->timetable?->academicCycleSection?->school_id,
            );

            $substitution->delete();
        });
    }

    /**
     * Refuse a teacher who is timetabled to teach at that time, at this campus
     * or another campus of the organization.
     *
     * A teacher whose own lesson somebody else already covers that day is free.
     */
    private function failIfTheTeacherHasTheirOwnLesson(User $replacementTeacher, Timetable $timetable, TimetableTimeSlot $slot, CarbonInterface $date): void
    {
        $school = $timetable->academicCycleSection->school;
        $lessons = $this->conflictChecker->lessonsTaughtBy($replacementTeacher, $date, $school);

        foreach ($lessons as $lesson) {
            if ($lesson['start_time'] >= $slot->stop_time || $lesson['stop_time'] <= $slot->start_time) {
                continue;
            }

            $coveredBySomeoneElse = TimetableSubstitution::query()
                ->where('timetable_time_slot_id', $lesson['time_slot_id'])
                ->where('weekday_id', $lesson['weekday_id'])
                ->whereDate('substituted_on', $date)
                ->where('replacement_teacher_id', '!=', $replacementTeacher->id)
                ->exists();

            if (!$coveredBySomeoneElse) {
                throw new InvalidValueException("$replacementTeacher->name teaches {$lesson['timetable']->name} at that time on that day.");
            }
        }
    }

    /**
     * Refuse a teacher who is on leave that day, at this campus or another.
     */
    private function failIfTheTeacherIsAway(User $replacementTeacher, CarbonInterface $date): void
    {
        $isAway = StaffProfile::query()
            ->where('user_id', $replacementTeacher->id)
            ->get()
            ->contains(fn (StaffProfile $profile): bool => $this->availability->isAway($profile, $date));

        if ($isAway) {
            throw new InvalidValueException("$replacementTeacher->name is on leave that day.");
        }
    }

    /**
     * Refuse a teacher who already covers a lesson that overlaps this one.
     */
    private function failIfTheTeacherIsAlreadyCovering(User $replacementTeacher, TimetableTimeSlot $slot, CarbonInterface $date): void
    {
        $isBusy = TimetableSubstitution::query()
            ->join('timetable_time_slots', 'timetable_time_slots.id', '=', 'timetable_substitutions.timetable_time_slot_id')
            ->where('timetable_substitutions.replacement_teacher_id', $replacementTeacher->id)
            ->whereDate('timetable_substitutions.substituted_on', $date)
            ->where('timetable_time_slots.start_time', '<', $slot->stop_time)
            ->where('timetable_time_slots.stop_time', '>', $slot->start_time)
            ->exists();

        if ($isBusy) {
            throw new InvalidValueException("$replacementTeacher->name already covers another lesson at that time on that day.");
        }
    }

    /**
     * Check that the dated replacement refers to one real, active lesson.
     */
    private function failIfRecordsDoNotFit(Timetable $timetable, TimetableTimeSlot $slot, ?Weekday $weekday, User $replacementTeacher, CarbonInterface $date): void
    {
        $schoolId = $timetable->academicCycleSection->school_id;

        if (!$timetable->isPublished() || !$timetable->academicPeriod->acceptsNewWork() || !$timetable->academicPeriod->academicYear->acceptsNewWork()) {
            throw new InvalidValueException('A substitution can only be recorded while its published timetable and academic cycle accept new work.');
        }

        if ($slot->timetable_id !== $timetable->id || $weekday === null || !TimetableRecord::query()
            ->where('timetable_time_slot_id', $slot->id)
            ->where('weekday_id', $weekday->id)
            ->exists()) {
            throw new InvalidValueException('Choose a scheduled entry from this timetable.');
        }

        $period = $timetable->academicPeriod;

        if (($period->starts_on !== null && $date->lt($period->starts_on)) || ($period->ends_on !== null && $date->gt($period->ends_on))) {
            throw new InvalidValueException('The selected date is outside the term this timetable belongs to.');
        }

        if (($timetable->effective_from !== null && $date->lt($timetable->effective_from)) || ($timetable->effective_to !== null && $date->gt($timetable->effective_to))) {
            throw new InvalidValueException('The selected date is outside the dates this timetable is in use.');
        }

        if (strcasecmp($weekday->name, $date->format('l')) !== 0) {
            throw new InvalidValueException('The selected date does not fall on the scheduled weekday.');
        }

        $closure = $this->calendar->closureOn($schoolId, $timetable->academic_cycle_section_id, $date);

        if ($closure !== null) {
            throw new InvalidValueException("The school is shut on {$date->format('j M Y')} for {$closure->title}, so the lesson needs no cover.");
        }

        if (!$replacementTeacher->belongsToSchool($schoolId) || !$replacementTeacher->hasRole(Role::Teacher->value)) {
            throw new InvalidValueException('The replacement must be an active teacher at this school.');
        }
    }
}
