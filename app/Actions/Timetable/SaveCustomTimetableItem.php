<?php

namespace App\Actions\Timetable;

use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Models\CustomTimetableItem;
use App\Models\School;
use App\Models\TimetableRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Name the things a timetable holds that are not lessons, such as a break.
 *
 * A published timetable stops changing, so an item it shows is never deleted
 * from under it.
 */
class SaveCustomTimetableItem
{
    /**
     * @throws InvalidValueException when the school already has an item with that name
     */
    public function create(string $name): CustomTimetableItem
    {
        try {
            return DB::transaction(function () use ($name): CustomTimetableItem {
                School::query()->whereKey(current_school_id())->lockForUpdate()->firstOrFail();
                $this->refuseATakenName($name);

                return CustomTimetableItem::create(['name' => $name, 'school_id' => current_school_id()]);
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('This school already has an item with that name.');
        }
    }

    /**
     * @throws InvalidValueException when another item has that name
     */
    public function rename(CustomTimetableItem $item, string $name): CustomTimetableItem
    {
        try {
            return DB::transaction(function () use ($item, $name): CustomTimetableItem {
                School::query()->whereKey($item->school_id)->lockForUpdate()->firstOrFail();
                $this->refuseATakenName($name, $item);

                $item->update(['name' => $name]);

                return $item;
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('This school already has an item with that name.');
        }
    }

    /**
     * Delete an item and take it off the draft timetables that hold it.
     *
     * @throws InvalidValueException when a published or archived timetable shows it
     */
    public function delete(CustomTimetableItem $item): void
    {
        DB::transaction(function () use ($item): void {
            $cells = TimetableRecord::query()
                ->where('timetable_time_slot_weekdayable_type', $item->getMorphClass())
                ->where('timetable_time_slot_weekdayable_id', $item->getKey());

            $isOnAFixedTimetable = (clone $cells)
                ->whereHas('timeSlot.timetable', fn (Builder $query) => $query->where('status', '!=', TimetableStatus::Draft->value))
                ->exists();

            if ($isOnAFixedTimetable) {
                throw new InvalidValueException("{$item->name} is on a published timetable, so it stays. Rename it instead.");
            }

            TimetableRecord::query()->whereKey($cells->pluck('id'))->delete();
            $item->delete();
        });
    }

    /**
     * @throws InvalidValueException when the school already has an item with that name
     */
    private function refuseATakenName(string $name, ?CustomTimetableItem $except = null): void
    {
        $isTaken = CustomTimetableItem::query()
            ->inSchool()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($except !== null, fn (Builder $query) => $query->whereKeyNot($except->getKey()))
            ->exists();

        if ($isTaken) {
            throw new InvalidValueException('This school already has an item with that name.');
        }
    }
}
