<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\SupervisionRole;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingSupervision;
use App\Models\Dormitory;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Put a member of staff on duty in a boarding house, and take them off again.
 *
 * The rota is kept rather than replaced, so a school can still answer who was
 * on duty on the night something happened.
 */
class AssignBoardingSupervisor
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Put the member of staff on duty.
     *
     * @throws InvalidValueException when the person cannot take the duty
     */
    public function assign(
        Dormitory $dormitory,
        User $staff,
        SupervisionRole $role,
        ?Carbon $startsOn = null,
        ?User $actor = null,
    ): BoardingSupervision {
        if (!$staff->belongsToSchool($dormitory->school_id)) {
            throw new InvalidValueException('That member of staff does not work at this campus.');
        }

        if ($staff->studentRecord()->exists()) {
            throw new InvalidValueException('A learner cannot supervise a boarding house.');
        }

        $startsOn ??= now();

        $running = BoardingSupervision::query()
            ->where('dormitory_id', $dormitory->id)
            ->where('user_id', $staff->id)
            ->where('role', $role)
            ->onDuty($startsOn->toDateString())
            ->first();

        if ($running !== null) {
            return $running;
        }

        $supervision = BoardingSupervision::create([
            'school_id' => $dormitory->school_id,
            'dormitory_id' => $dormitory->id,
            'user_id' => $staff->id,
            'role' => $role,
            'starts_on' => $startsOn,
            'assigned_by' => $actor === null ? auth()->id() : $actor->id,
        ]);

        $this->auditor->record(
            AuditAction::BoardingSupervisionChanged,
            $supervision,
            ['house' => $dormitory->name, 'staff' => $staff->name, 'role' => $role->value, 'from' => $startsOn->toDateString()],
            $actor,
            $dormitory->school_id,
        );

        return $supervision;
    }

    /**
     * Take the member of staff off duty from a date.
     *
     * @throws InvalidValueException when the duty already ended
     */
    public function end(BoardingSupervision $supervision, ?Carbon $endsOn = null, ?User $actor = null): BoardingSupervision
    {
        if ($supervision->ends_on !== null) {
            throw new InvalidValueException('This duty has already ended.');
        }

        $endsOn ??= now();

        if ($endsOn->lessThan($supervision->starts_on)) {
            throw new InvalidValueException('A duty cannot end before it started.');
        }

        $supervision->ends_on = $endsOn;
        $supervision->save();

        $this->auditor->record(
            AuditAction::BoardingSupervisionChanged,
            $supervision,
            ['ended_on' => $endsOn->toDateString()],
            $actor,
            $supervision->school_id,
        );

        return $supervision;
    }
}
