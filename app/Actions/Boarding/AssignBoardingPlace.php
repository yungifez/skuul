<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\DormitoryBedStatus;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingPlace;
use App\Models\DormitoryBed;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Give a learner a bed, or take their boarding place away.
 *
 * Two people admitting boarders at once must never put two children in one
 * bed, so the bed is locked while the place is written. Giving the same bed
 * to the same learner again changes nothing, so a retry is safe.
 */
class AssignBoardingPlace
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Put the learner in a bed.
     *
     * @throws InvalidValueException when the bed is taken, closed, or on another campus
     */
    public function assign(
        StudentRecord $enrollment,
        DormitoryBed $bed,
        ?User $actor = null,
        ?string $reason = null,
        ?CarbonInterface $effectiveOn = null,
    ): BoardingPlace {
        return DB::transaction(function () use ($enrollment, $bed, $actor, $reason, $effectiveOn): BoardingPlace {
            $bed = DormitoryBed::query()->lockForUpdate()->with('room.dormitory')->findOrFail($bed->getKey());

            $this->refuseWhatDoesNotFit($enrollment, $bed);

            $current = BoardingPlace::currentFor($enrollment);

            if ($current !== null && $current->dormitory_bed_id === $bed->id) {
                return $current;
            }

            $place = BoardingPlace::create([
                'school_id' => $enrollment->school_id,
                'student_record_id' => $enrollment->id,
                'dormitory_bed_id' => $bed->id,
                'academic_year_id' => current_academic_year_id(),
                'effective_on' => $effectiveOn ?? now(),
                'reason' => $reason,
                'changed_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::BoardingPlaceChanged,
                $enrollment,
                [
                    'bed' => $bed->name,
                    'room' => $bed->room?->name,
                    'house' => $bed->room?->dormitory?->name,
                    'reason' => $reason,
                ],
                $actor,
                $enrollment->school_id,
            );

            return $place;
        });
    }

    /**
     * Record that the learner has stopped boarding.
     *
     * @throws InvalidValueException when the learner was not boarding
     */
    public function end(
        StudentRecord $enrollment,
        string $reason,
        ?User $actor = null,
        ?CarbonInterface $effectiveOn = null,
    ): BoardingPlace {
        $current = BoardingPlace::currentFor($enrollment);

        if ($current === null || !$current->isBoarding()) {
            throw new InvalidValueException('This learner is not boarding.');
        }

        if (trim($reason) === '') {
            throw new InvalidValueException('Say why the learner is leaving the house.');
        }

        $place = BoardingPlace::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'dormitory_bed_id' => null,
            'academic_year_id' => current_academic_year_id(),
            'effective_on' => $effectiveOn ?? now(),
            'reason' => $reason,
            'changed_by' => $actor === null ? auth()->id() : $actor->id,
        ]);

        $this->auditor->record(
            AuditAction::BoardingPlaceChanged,
            $enrollment,
            ['bed' => null, 'reason' => $reason],
            $actor,
            $enrollment->school_id,
        );

        return $place;
    }

    /**
     * Refuse anything that would put a child in the wrong bed.
     *
     * @throws InvalidValueException
     */
    private function refuseWhatDoesNotFit(StudentRecord $enrollment, DormitoryBed $bed): void
    {
        if ($enrollment->status->isClosed()) {
            throw new InvalidValueException('This enrollment is closed, so it cannot take a bed.');
        }

        if ($bed->school_id !== $enrollment->school_id) {
            throw new InvalidValueException('That bed is on another campus.');
        }

        if (!$bed->is_active || $bed->status !== DormitoryBedStatus::Available || $bed->room === null || !$bed->room->is_active || !$bed->room->dormitory?->is_active) {
            throw new InvalidValueException('That bed is out of use.');
        }

        $occupant = BoardingPlace::query()
            ->current()
            ->where('dormitory_bed_id', $bed->id)
            ->where('student_record_id', '!=', $enrollment->id)
            ->exists();

        if ($occupant) {
            throw new InvalidValueException('Somebody already sleeps in that bed.');
        }
    }
}
