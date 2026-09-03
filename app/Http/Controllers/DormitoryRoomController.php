<?php

namespace App\Http\Controllers;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreDormitoryRoomRequest;
use App\Http\Requests\UpdateDormitoryRoomRequest;
use App\Models\Dormitory;
use App\Models\DormitoryRoom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * Manage the rooms inside boarding houses.
 */
class DormitoryRoomController extends Controller
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Add a room to a house.
     */
    public function store(StoreDormitoryRoomRequest $request, Dormitory $dormitory): RedirectResponse
    {
        $this->authorize('update', $dormitory);

        DB::transaction(function () use ($dormitory, $request): void {
            $room = $dormitory->rooms()->create([
                'school_id' => $dormitory->school_id,
                ...$request->validated(),
            ]);

            $this->auditor->record(
                AuditAction::BoardingRoomChanged,
                $room,
                ['change' => 'created', 'house' => $dormitory->name],
            );
        });

        return redirect()
            ->route('dormitories.show', $dormitory->id)
            ->with('success', 'Room added.');
    }

    /**
     * Change a room or take it out of use.
     */
    public function update(UpdateDormitoryRoomRequest $request, DormitoryRoom $room): RedirectResponse
    {
        $room->loadMissing('dormitory');
        $this->authorize('update', $room->dormitory);

        $isActive = $request->boolean('is_active');

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

        DB::transaction(function () use ($room, $request, $isActive): void {
            $room->update([
                ...$request->validated(),
                'is_active' => $isActive,
            ]);

            $this->auditor->record(
                AuditAction::BoardingRoomChanged,
                $room,
                ['change' => 'updated', 'is_active' => $isActive],
            );
        });

        return redirect()
            ->route('dormitories.show', $room->dormitory_id)
            ->with('success', "$room->name was updated.");
    }
}
