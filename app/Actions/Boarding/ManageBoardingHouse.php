<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingPlace;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use Illuminate\Support\Facades\DB;

/**
 * Open, change, and close the boarding houses of one campus.
 *
 * A house never closes while a child sleeps in it. Closing locks the house,
 * and a bed placement reads the house under the same lock, so the two cannot
 * pass each other.
 */
class ManageBoardingHouse
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Open a house with its rooms and beds.
     *
     * A house is no use without somewhere to sleep, so the rooms and beds are
     * made here rather than left as a second job.
     */
    public function open(int $schoolId, string $name, string $label, ?string $notes, int $rooms, int $bedsPerRoom): Dormitory
    {
        return DB::transaction(function () use ($schoolId, $name, $label, $notes, $rooms, $bedsPerRoom): Dormitory {
            $dormitory = Dormitory::create([
                'school_id' => $schoolId,
                'name' => $name,
                'label' => $label,
                'notes' => $notes,
            ]);

            for ($room = 1; $room <= $rooms; $room++) {
                $created = DormitoryRoom::create([
                    'school_id' => $schoolId,
                    'dormitory_id' => $dormitory->id,
                    'name' => "Room $room",
                ]);

                for ($bed = 1; $bed <= $bedsPerRoom; $bed++) {
                    DormitoryBed::create([
                        'school_id' => $schoolId,
                        'dormitory_room_id' => $created->id,
                        'name' => "Bed $bed",
                    ]);
                }
            }

            $this->auditor->record(AuditAction::BoardingHouseChanged, $dormitory, ['change' => 'created', 'rooms' => $rooms]);

            return $dormitory;
        });
    }

    /**
     * Change the details of a house, and open or close it.
     *
     * @throws InvalidValueException when the house would close with boarders in it
     */
    public function change(Dormitory $dormitory, string $name, string $label, ?string $notes, bool $isActive): Dormitory
    {
        return DB::transaction(function () use ($dormitory, $name, $label, $notes, $isActive): Dormitory {
            $dormitory = Dormitory::query()->lockForUpdate()->findOrFail($dormitory->id);
            $wasActive = $dormitory->is_active;

            if ($wasActive && !$isActive && $this->hasCurrentBoarders($dormitory)) {
                throw new InvalidValueException("Move the boarders out of {$dormitory->name} before closing it.");
            }

            $dormitory->update(['name' => $name, 'label' => $label, 'notes' => $notes, 'is_active' => $isActive]);

            $this->auditor->record(AuditAction::BoardingHouseChanged, $dormitory, [
                'change' => match (true) {
                    $wasActive && !$isActive => 'archived',
                    !$wasActive && $isActive => 'reopened',
                    default => 'updated',
                },
                'is_active' => $isActive,
            ]);

            return $dormitory;
        });
    }

    /**
     * Check whether any learner sleeps in the house now.
     */
    public function hasCurrentBoarders(Dormitory $dormitory): bool
    {
        return BoardingPlace::query()
            ->current()
            ->whereIn('dormitory_bed_id', DormitoryBed::query()
                ->select('dormitory_beds.id')
                ->join('dormitory_rooms', 'dormitory_rooms.id', '=', 'dormitory_beds.dormitory_room_id')
                ->where('dormitory_rooms.dormitory_id', $dormitory->id))
            ->exists();
    }
}
