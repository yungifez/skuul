<?php

namespace App\Services\Boarding;

use App\Enums\DormitoryBedStatus;
use App\Models\BoardingPlace;
use App\Models\Dormitory;
use App\Models\OvernightLeave;
use App\Models\StudentRecord;
use Illuminate\Support\Collection;

/**
 * Who sleeps where, and who is not in the building tonight.
 *
 * This is the list staff read at lights out, so it has to be right without
 * anybody keeping a second copy of it.
 */
class BoardingRoster
{
    /**
     * Get every learner sleeping in one house, by room.
     *
     * @return Collection<int, BoardingPlace>
     */
    public function inDormitory(Dormitory $dormitory): Collection
    {
        return BoardingPlace::query()
            ->current()
            ->whereNotNull('dormitory_bed_id')
            ->whereHas('bed.room', fn ($room) => $room->where('dormitory_id', $dormitory->id))
            ->with(['bed.room', 'studentRecord.user', 'studentRecord.academicCycleSection.academicLevel'])
            ->get()
            ->sortBy($this->bedOrder(...))
            ->values()
            ->toBase();
    }

    /**
     * Get the name a bed sorts under, so a room reads top to bottom.
     */
    private function bedOrder(BoardingPlace $place): string
    {
        $bed = $place->bed;

        if ($bed === null) {
            return '';
        }

        $room = $bed->room;

        return ($room === null ? '' : $room->name).' '.$bed->name;
    }

    /**
     * Get the learners who should be away from one house tonight.
     *
     * @return Collection<int, OvernightLeave>
     */
    public function awayFrom(Dormitory $dormitory, ?string $night = null): Collection
    {
        $boarders = BoardingPlace::query()
            ->current()
            ->whereNotNull('dormitory_bed_id')
            ->whereHas('bed.room', fn ($room) => $room->where('dormitory_id', $dormitory->id))
            ->pluck('student_record_id');

        return OvernightLeave::query()
            ->awayOn($night)
            ->whereIn('student_record_id', $boarders)
            ->with('studentRecord.user')
            ->get()
            ->toBase();
    }

    /**
     * Count the beds in a house and how many of them are taken.
     *
     * @return array{beds: int, taken: int, free: int, unavailable: int, away: int}
     */
    public function occupancyOf(Dormitory $dormitory, ?string $night = null): array
    {
        $bedsQuery = $dormitory->beds()
            ->where('dormitory_beds.is_active', true)
            ->whereHas('room', fn ($room) => $room->where('is_active', true));
        $beds = (clone $bedsQuery)->count();
        $unavailable = (clone $bedsQuery)
            ->where('dormitory_beds.status', '!=', DormitoryBedStatus::Available->value)
            ->count();
        $taken = BoardingPlace::countInDormitory($dormitory->id);

        return [
            'beds' => $beds,
            'taken' => $taken,
            'free' => max($beds - $taken - $unavailable, 0),
            'unavailable' => $unavailable,
            'away' => $this->awayFrom($dormitory, $night)->count(),
        ];
    }

    /**
     * Get where one learner sleeps now, in words.
     */
    public function placeOf(StudentRecord $enrollment): ?string
    {
        $place = BoardingPlace::currentFor($enrollment);

        if ($place === null || !$place->isBoarding()) {
            return null;
        }

        $bed = $place->bed;

        if ($bed === null) {
            return null;
        }

        $room = $bed->room;
        $house = $room === null ? null : $room->dormitory;

        return trim(implode(' · ', array_filter([$house?->name, $room?->name, $bed->name])), ' ·');
    }
}
