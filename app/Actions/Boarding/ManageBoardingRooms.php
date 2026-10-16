<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\DormitoryBedStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Add, rename and take out of use the rooms and beds inside a boarding house.
 *
 * A room or bed that a learner sleeps in tonight stays in use. The office
 * moves the boarder first, so nobody is left in a bed the roll cannot see.
 */
class ManageBoardingRooms
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Add a room to a house.
     */
    public function addRoom(Dormitory $dormitory, string $name, ?string $floor = null): DormitoryRoom
    {
        return DB::transaction(function () use ($dormitory, $name, $floor): DormitoryRoom {
            $room = $dormitory->rooms()->create([
                'school_id' => $dormitory->school_id,
                'name' => $name,
                'floor' => $floor,
            ]);

            $this->auditor->record(AuditAction::BoardingRoomChanged, $room, ['change' => 'created', 'house' => $dormitory->name]);

            return $room;
        });
    }

    /**
     * Rename a room or take it out of use.
     *
     * @throws InvalidValueException when the room still has boarders and would leave use
     */
    public function updateRoom(DormitoryRoom $room, string $name, ?string $floor, bool $isActive): DormitoryRoom
    {
        if (!$isActive && $room->beds()->whereHas('places', function (Builder $places): void {
            // BoardingPlace::scopeCurrent() is written out here because
            // relation closures receive a generic Eloquent builder.
            $places->whereIn('boarding_places.id', function ($newest): void {
                $newest->from('boarding_places')
                    ->selectRaw('max(id)')
                    ->groupBy('student_record_id');
            });
        })->exists()) {
            throw new InvalidValueException('Move the current boarders before taking this room out of use.');
        }

        return DB::transaction(function () use ($room, $name, $floor, $isActive): DormitoryRoom {
            $room->update(['name' => $name, 'floor' => $floor, 'is_active' => $isActive]);

            $this->auditor->record(AuditAction::BoardingRoomChanged, $room, ['change' => 'updated', 'is_active' => $isActive]);

            return $room;
        });
    }

    /**
     * Add one bed to a room.
     */
    public function addBed(DormitoryRoom $room, string $name): DormitoryBed
    {
        return DB::transaction(function () use ($room, $name): DormitoryBed {
            $bed = $room->beds()->create([
                'school_id' => $room->school_id,
                'name' => $name,
            ]);

            $this->auditor->record(AuditAction::BoardingBedChanged, $bed, ['change' => 'created', 'room' => $room->name]);

            return $bed;
        });
    }

    /**
     * Rename a bed or change whether it can take a learner.
     *
     * @throws InvalidValueException when a boarder sleeps in a bed that would close
     */
    public function updateBed(DormitoryBed $bed, string $name, DormitoryBedStatus $status, ?string $reason = null): DormitoryBed
    {
        if ($bed->isTaken() && !$status->isAssignable()) {
            throw new InvalidValueException('Move the current boarder before making this bed unavailable.');
        }

        return DB::transaction(function () use ($bed, $name, $status, $reason): DormitoryBed {
            $bed->update([
                'name' => $name,
                'status' => $status,
                'status_reason' => $reason,
                'is_active' => $status !== DormitoryBedStatus::Retired,
            ]);

            $this->auditor->record(AuditAction::BoardingBedChanged, $bed, ['change' => 'updated', 'status' => $status->value]);

            return $bed;
        });
    }
}
