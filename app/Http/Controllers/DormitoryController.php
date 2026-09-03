<?php

namespace App\Http\Controllers;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreDormitoryRequest;
use App\Http\Requests\UpdateDormitoryRequest;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use App\Services\Boarding\BoardingRoster;
use App\Traits\ListsSchoolPeople;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * The boarding houses of one campus, and who sleeps in them.
 */
class DormitoryController extends Controller
{
    use ListsSchoolPeople;

    public function __construct(private RecordAuditEvent $auditor)
    {
        $this->authorizeResource(Dormitory::class, 'dormitory');
    }

    /**
     * Show every house with how full it is.
     */
    public function index(BoardingRoster $roster): View
    {
        $dormitories = Dormitory::inSchool()->orderBy('name')->get();

        return view('pages.boarding.index', [
            'dormitories' => $dormitories,
            'occupancy' => $dormitories->mapWithKeys(
                fn (Dormitory $dormitory): array => [$dormitory->id => $roster->occupancyOf($dormitory)],
            ),
        ]);
    }

    /**
     * Show the form for opening a house.
     */
    public function create(): View
    {
        return view('pages.boarding.create');
    }

    /**
     * Show the form for changing a house.
     */
    public function edit(Dormitory $dormitory): View
    {
        return view('pages.boarding.edit', ['dormitory' => $dormitory]);
    }

    /**
     * Open a house with its rooms and beds.
     */
    public function store(StoreDormitoryRequest $request): RedirectResponse
    {
        $dormitory = DB::transaction(function () use ($request): Dormitory {
            $dormitory = Dormitory::create([
                'school_id' => current_school_id(),
                'name' => $request->validated('name'),
                'label' => $request->validated('label'),
                'notes' => $request->validated('notes'),
            ]);

            // A house is no use without somewhere to sleep, so the rooms and
            // beds are made here rather than left as a second job.
            for ($room = 1; $room <= (int) $request->validated('rooms'); $room++) {
                $created = DormitoryRoom::create([
                    'school_id' => $dormitory->school_id,
                    'dormitory_id' => $dormitory->id,
                    'name' => "Room $room",
                ]);

                for ($bed = 1; $bed <= (int) $request->validated('beds_per_room'); $bed++) {
                    DormitoryBed::create([
                        'school_id' => $dormitory->school_id,
                        'dormitory_room_id' => $created->id,
                        'name' => "Bed $bed",
                    ]);
                }
            }

            $this->auditor->record(
                AuditAction::BoardingHouseChanged,
                $dormitory,
                ['change' => 'created', 'rooms' => (int) $request->validated('rooms')],
            );

            return $dormitory;
        });

        return redirect()
            ->route('dormitories.show', $dormitory->id)
            ->with('success', "$dormitory->name is open.");
    }

    /**
     * Change a house without changing its boarding history.
     */
    public function update(UpdateDormitoryRequest $request, Dormitory $dormitory): RedirectResponse
    {
        $isActive = $request->boolean('is_active');

        if (!$isActive && $this->hasCurrentBoarders($dormitory)) {
            throw new InvalidValueException('Move the current boarders before closing this house.');
        }

        DB::transaction(function () use ($dormitory, $request, $isActive): void {
            $dormitory->update([
                ...$request->validated(),
                'is_active' => $isActive,
            ]);

            $this->auditor->record(
                AuditAction::BoardingHouseChanged,
                $dormitory,
                ['change' => 'updated', 'is_active' => $isActive],
            );
        });

        return redirect()
            ->route('dormitories.show', $dormitory->id)
            ->with('success', "$dormitory->name was updated.");
    }

    /**
     * Archive a house while preserving its rooms, beds, and history.
     */
    public function destroy(Dormitory $dormitory): RedirectResponse
    {
        if ($this->hasCurrentBoarders($dormitory)) {
            throw new InvalidValueException('Move the current boarders before archiving this house.');
        }

        DB::transaction(function () use ($dormitory): void {
            $dormitory->update(['is_active' => false]);

            $this->auditor->record(
                AuditAction::BoardingHouseChanged,
                $dormitory,
                ['change' => 'archived'],
            );
        });

        return redirect()
            ->route('dormitories.index')
            ->with('success', "$dormitory->name was archived.");
    }

    /**
     * Show one house: who sleeps where, who is out, and who is on duty.
     */
    public function show(Dormitory $dormitory, BoardingRoster $roster): View
    {
        $dormitory->loadMissing(['boardingResidence', 'rooms.beds']);

        $places = $roster->inDormitory($dormitory);
        $occupiedBy = $places->keyBy('dormitory_bed_id');
        $roomDetails = $dormitory->rooms->map(function (DormitoryRoom $room) use ($occupiedBy): array {
            $beds = $room->beds->map(function (DormitoryBed $bed) use ($occupiedBy): array {
                $place = $occupiedBy->get($bed->id);
                $status = $bed->status;

                return [
                    'id' => $bed->id,
                    'name' => $bed->name,
                    'status' => $status->value,
                    'status_label' => $status->label(),
                    'status_reason' => $bed->status_reason,
                    'is_active' => $bed->is_active,
                    'is_occupied' => $place !== null,
                    'occupant_name' => $place?->studentRecord?->user?->name,
                    'occupant_admission_number' => $place?->studentRecord?->admission_number,
                    'update_url' => route('dormitory-beds.update', $bed),
                    'leave_url' => $place === null
                        ? null
                        : route('boarding-places.destroy', $place->student_record_id),
                ];
            })->values();

            return [
                'id' => $room->id,
                'name' => $room->name,
                'floor' => $room->floor,
                'is_active' => $room->is_active,
                'bed_count' => $beds->count(),
                'available_count' => $room->is_active
                    ? $beds->where('is_active', true)
                        ->where('status', 'available')
                        ->where('is_occupied', false)
                        ->count()
                    : 0,
                'occupied_count' => $beds->where('is_occupied', true)->count(),
                'unavailable_count' => !$room->is_active
                    ? $beds->count()
                    : $beds->filter(
                        fn (array $bed): bool => !$bed['is_active'] || $bed['status'] !== 'available',
                    )->count(),
                'update_url' => route('dormitory-rooms.update', $room),
                'add_bed_url' => route('dormitory-beds.store', $room),
                'beds' => $beds->all(),
            ];
        })->values()->all();
        $roomSummaries = collect($roomDetails)->mapWithKeys(
            fn (array $room): array => [$room['id'] => $room],
        )->all();

        return view('pages.boarding.show', [
            'dormitory' => $dormitory,
            'places' => $places,
            'occupiedBy' => $occupiedBy,
            'roomDetails' => $roomDetails,
            'roomSummaries' => $roomSummaries,
            'away' => $roster->awayFrom($dormitory),
            'occupancy' => $roster->occupancyOf($dormitory),
            'onDuty' => $dormitory->supervisions()->onDuty()->with('user')->get(),
            'learners' => $this->schoolLearners(),
            'canManage' => auth()->user()?->can('manage boarding') === true,
        ]);
    }

    private function hasCurrentBoarders(Dormitory $dormitory): bool
    {
        return $dormitory->beds()
            ->whereHas('places', fn ($places) => $places->current())
            ->exists();
    }
}
