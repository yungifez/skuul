<?php

namespace App\Http\Controllers;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\DormitoryBedStatus;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreDormitoryBedRequest;
use App\Http\Requests\UpdateDormitoryBedRequest;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * Manage the beds inside boarding rooms.
 */
class DormitoryBedController extends Controller
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Add one bed to a room.
     */
    public function store(StoreDormitoryBedRequest $request, DormitoryRoom $room): RedirectResponse
    {
        $room->loadMissing('dormitory');
        $this->authorize('update', $room->dormitory);

        DB::transaction(function () use ($room, $request): void {
            $bed = $room->beds()->create([
                'school_id' => $room->school_id,
                ...$request->validated(),
            ]);

            $this->auditor->record(
                AuditAction::BoardingBedChanged,
                $bed,
                ['change' => 'created', 'room' => $room->name],
            );
        });

        return redirect()
            ->route('dormitories.show', $room->dormitory_id)
            ->with('success', 'Bed added.');
    }

    /**
     * Rename a bed or change its availability.
     */
    public function update(UpdateDormitoryBedRequest $request, DormitoryBed $bed): RedirectResponse
    {
        $bed->loadMissing('room.dormitory');
        $this->authorize('update', $bed->room->dormitory);

        $status = DormitoryBedStatus::from($request->validated('status'));

        if ($bed->isTaken() && !$status->isAssignable()) {
            throw new InvalidValueException('Move the current boarder before making this bed unavailable.');
        }

        DB::transaction(function () use ($bed, $request, $status): void {
            $bed->update([
                'name' => $request->validated('name'),
                'status' => $status,
                'status_reason' => $request->validated('status_reason'),
                'is_active' => $status !== DormitoryBedStatus::Retired,
            ]);

            $this->auditor->record(
                AuditAction::BoardingBedChanged,
                $bed,
                ['change' => 'updated', 'status' => $status->value],
            );
        });

        return redirect()
            ->route('dormitories.show', $bed->room->dormitory_id)
            ->with('success', "$bed->name was updated.");
    }
}
