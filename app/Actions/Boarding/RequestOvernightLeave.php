<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\OvernightLeaveStatus;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingPlace;
use App\Models\OvernightLeave;
use App\Models\StudentRecord;
use App\Models\User;

/**
 * Ask for a night away from the boarding house.
 *
 * Nobody is allowed out on a request that overlaps one already approved, so
 * two people cannot sign the same child out to two places.
 */
class RequestOvernightLeave
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Record the request.
     *
     * @throws InvalidValueException when the learner is not boarding or the nights clash
     */
    public function request(
        StudentRecord $enrollment,
        string $leavesOn,
        string $returnsOn,
        string $destination,
        ?string $contact = null,
        ?string $reason = null,
        ?User $actor = null,
    ): OvernightLeave {
        $place = BoardingPlace::currentFor($enrollment);

        if ($place === null || !$place->isBoarding()) {
            throw new InvalidValueException('This learner does not board, so there is no house to leave.');
        }

        if (strtotime($returnsOn) < strtotime($leavesOn)) {
            throw new InvalidValueException('A learner cannot come back before they leave.');
        }

        $clash = OvernightLeave::query()
            ->where('student_record_id', $enrollment->id)
            ->whereIn('status', [OvernightLeaveStatus::Requested, OvernightLeaveStatus::Approved])
            ->where('leaves_on', '<=', $returnsOn)
            ->where('returns_on', '>=', $leavesOn)
            ->exists();

        if ($clash) {
            throw new InvalidValueException('This learner already has leave covering one of those nights.');
        }

        $leave = OvernightLeave::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'leaves_on' => $leavesOn,
            'returns_on' => $returnsOn,
            'destination' => $destination,
            'contact' => $contact,
            'reason' => $reason,
            'requested_by' => $actor === null ? auth()->id() : $actor->id,
        ]);

        $this->auditor->record(
            AuditAction::OvernightLeaveRequested,
            $leave,
            ['from' => $leavesOn, 'to' => $returnsOn, 'destination' => $destination],
            $actor,
            $enrollment->school_id,
        );

        return $leave;
    }
}
