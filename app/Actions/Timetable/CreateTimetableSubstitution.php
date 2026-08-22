<?php

namespace App\Actions\Timetable;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableSubstitution;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class CreateTimetableSubstitution
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    public function create(Timetable $timetable, TimetableTimeSlot $slot, int $weekdayId, User $replacementTeacher, CarbonInterface $date, string $reason, User $actor): TimetableSubstitution
    {
        $timetable->loadMissing(['academicCycleSection', 'academicPeriod.academicYear']);
        $weekday = Weekday::query()->find($weekdayId);

        $this->failIfRecordsDoNotFit($timetable, $slot, $weekday, $replacementTeacher, $date);

        if (TimetableSubstitution::query()
            ->where('timetable_time_slot_id', $slot->id)
            ->where('weekday_id', $weekdayId)
            ->whereDate('substituted_on', $date)
            ->exists()) {
            throw new InvalidValueException('That timetable entry already has a substitution for this date.');
        }

        return DB::transaction(function () use ($timetable, $slot, $weekdayId, $replacementTeacher, $date, $reason, $actor): TimetableSubstitution {
            $substitution = TimetableSubstitution::create(['timetable_id' => $timetable->id, 'timetable_time_slot_id' => $slot->id, 'weekday_id' => $weekdayId, 'replacement_teacher_id' => $replacementTeacher->id, 'substituted_on' => $date->toDateString(), 'reason' => $reason, 'approved_by' => $actor->id]);
            $this->auditor->record(AuditAction::TimetableSubstitutionCreated, $substitution, ['timetable_id' => $timetable->id, 'replacement_teacher_id' => $replacementTeacher->id, 'substituted_on' => $date->toDateString()], $actor, $timetable->academicCycleSection->school_id);

            return $substitution;
        });
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

        if (strcasecmp($weekday->name, $date->format('l')) !== 0) {
            throw new InvalidValueException('The selected date does not fall on the scheduled weekday.');
        }

        if (!$replacementTeacher->belongsToSchool($schoolId) || !$replacementTeacher->hasRole(Role::Teacher->value)) {
            throw new InvalidValueException('The replacement must be an active teacher at this school.');
        }
    }
}
