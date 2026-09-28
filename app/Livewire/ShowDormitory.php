<?php

namespace App\Livewire;

use App\Actions\Boarding\AssignBoardingPlace;
use App\Actions\Boarding\ManageBoardingRooms;
use App\Enums\DormitoryBedStatus;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingPlace;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use App\Models\StudentRecord;
use App\Services\Boarding\BoardingRoster;
use App\Traits\ListsSchoolPeople;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Show one boarding house room by room, and let the boarding office change it.
 *
 * Each room opens in place to show its beds. Rooms, beds and placements are
 * changed on the page, so the office never loses the room it was working in.
 */
class ShowDormitory extends Component
{
    use ListsSchoolPeople;

    public Dormitory $dormitory;

    public string $newRoomName = '';

    public string $newRoomFloor = '';

    public ?int $editingRoomId = null;

    public string $roomName = '';

    public string $roomFloor = '';

    public bool $roomIsActive = true;

    /** @var array<int|string, string> */
    public array $newBedNames = [];

    public ?int $editingBedId = null;

    public string $bedName = '';

    public string $bedStatus = '';

    public string $bedStatusReason = '';

    public ?int $leavingBedId = null;

    public string $leaveReason = '';

    public ?int $placeLearnerId = null;

    public ?int $placeBedId = null;

    public string $placeReason = '';

    public function mount(Dormitory $dormitory): void
    {
        Gate::authorize('view', $dormitory);

        $this->dormitory = $dormitory;
    }

    public function addRoom(ManageBoardingRooms $rooms): void
    {
        $this->authorizeManage();

        $this->validate([
            'newRoomName' => ['required', 'string', 'max:60', Rule::unique('dormitory_rooms', 'name')->where('dormitory_id', $this->dormitory->id)],
            'newRoomFloor' => ['nullable', 'string', 'max:40'],
        ], ['newRoomName.unique' => 'This house already has a room with that name.']);

        $rooms->addRoom($this->dormitory, $this->newRoomName, $this->newRoomFloor === '' ? null : $this->newRoomFloor);

        $this->reset('newRoomName', 'newRoomFloor');
    }

    public function editRoom(int $roomId): void
    {
        $room = $this->room($roomId);

        $this->editingRoomId = $room->id;
        $this->roomName = $room->name;
        $this->roomFloor = (string) $room->floor;
        $this->roomIsActive = $room->is_active;
        $this->resetValidation();
    }

    public function saveRoom(ManageBoardingRooms $rooms): void
    {
        $this->authorizeManage();
        $room = $this->room((int) $this->editingRoomId);

        $this->validate([
            'roomName' => ['required', 'string', 'max:60', Rule::unique('dormitory_rooms', 'name')->where('dormitory_id', $this->dormitory->id)->ignore($room->id)],
            'roomFloor' => ['nullable', 'string', 'max:40'],
            'roomIsActive' => ['boolean'],
        ]);

        try {
            $rooms->updateRoom($room, $this->roomName, $this->roomFloor === '' ? null : $this->roomFloor, $this->roomIsActive);
        } catch (InvalidValueException $exception) {
            $this->addError('roomIsActive', $exception->getMessage());

            return;
        }

        $this->reset('editingRoomId', 'roomName', 'roomFloor', 'roomIsActive');
    }

    public function addBed(int $roomId, ManageBoardingRooms $rooms): void
    {
        $this->authorizeManage();
        $room = $this->room($roomId);

        $this->validate([
            "newBedNames.$roomId" => ['required', 'string', 'max:40', Rule::unique('dormitory_beds', 'name')->where('dormitory_room_id', $room->id)],
        ], [
            "newBedNames.$roomId.required" => 'Name the bed.',
            "newBedNames.$roomId.unique" => 'This room already has a bed with that name.',
        ]);

        $rooms->addBed($room, $this->newBedNames[$roomId]);

        unset($this->newBedNames[$roomId]);
    }

    public function editBed(int $bedId): void
    {
        $bed = $this->bed($bedId);

        $this->editingBedId = $bed->id;
        $this->leavingBedId = null;
        $this->bedName = $bed->name;
        $this->bedStatus = $bed->status->value;
        $this->bedStatusReason = (string) $bed->status_reason;
        $this->resetValidation();
    }

    public function saveBed(ManageBoardingRooms $rooms): void
    {
        $this->authorizeManage();
        $bed = $this->bed((int) $this->editingBedId);

        $this->validate([
            'bedName' => ['required', 'string', 'max:40'],
            'bedStatus' => ['required', Rule::enum(DormitoryBedStatus::class)],
            'bedStatusReason' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $rooms->updateBed($bed, $this->bedName, DormitoryBedStatus::from($this->bedStatus), $this->bedStatusReason === '' ? null : $this->bedStatusReason);
        } catch (InvalidValueException $exception) {
            $this->addError('bedStatus', $exception->getMessage());

            return;
        }

        $this->reset('editingBedId', 'bedName', 'bedStatus', 'bedStatusReason');
    }

    public function startLeaving(int $bedId): void
    {
        $this->leavingBedId = $this->bed($bedId)->id;
        $this->editingBedId = null;
        $this->leaveReason = '';
        $this->resetValidation();
    }

    /**
     * Record that the learner in a bed has stopped boarding.
     */
    public function endPlacement(AssignBoardingPlace $assign): void
    {
        $this->authorizeManage();
        $this->validate(['leaveReason' => ['required', 'string', 'min:3', 'max:255']], ['leaveReason.required' => 'Say why they are leaving.']);

        $place = BoardingPlace::query()->current()->where('dormitory_bed_id', $this->bed((int) $this->leavingBedId)->id)->first();

        if ($place === null) {
            $this->addError('leaveReason', 'Nobody sleeps in this bed now.');

            return;
        }

        try {
            $assign->end(StudentRecord::query()->findOrFail($place->student_record_id), $this->leaveReason);
        } catch (InvalidValueException $exception) {
            $this->addError('leaveReason', $exception->getMessage());

            return;
        }

        $this->reset('leavingBedId', 'leaveReason');
    }

    /**
     * Give a learner a free bed in this house.
     */
    public function place(AssignBoardingPlace $assign): void
    {
        $this->authorizeManage();

        $this->validate([
            'placeLearnerId' => ['required', 'integer', Rule::exists('student_records', 'id')->where('school_id', current_school_id())],
            'placeBedId' => ['required', 'integer'],
            'placeReason' => ['nullable', 'string', 'max:255'],
        ], [
            'placeLearnerId.required' => 'Choose the learner.',
            'placeBedId.required' => 'Choose the bed.',
        ]);

        try {
            $assign->assign(
                enrollment: StudentRecord::inSchool()->findOrFail($this->placeLearnerId),
                bed: $this->bed((int) $this->placeBedId),
                reason: $this->placeReason === '' ? null : $this->placeReason,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('placeBedId', $exception->getMessage());

            return;
        }

        $this->reset('placeLearnerId', 'placeBedId', 'placeReason');
    }

    public function cancelEditing(): void
    {
        $this->reset('editingRoomId', 'editingBedId', 'leavingBedId', 'leaveReason');
        $this->resetValidation();
    }

    public function render(BoardingRoster $roster): View
    {
        $this->dormitory->load(['boardingResidence', 'rooms.beds']);
        $occupiedBy = $roster->inDormitory($this->dormitory)->keyBy('dormitory_bed_id');
        $canManage = auth()->user()?->can('manage boarding') === true;

        $assignableBeds = $this->dormitory->rooms->flatMap(
            fn (DormitoryRoom $room) => $room->beds->filter(
                fn (DormitoryBed $bed): bool => $room->is_active
                    && $bed->is_active
                    && $occupiedBy->get($bed->id) === null
                    && $bed->status === DormitoryBedStatus::Available,
            ),
        );

        return view('livewire.show-dormitory', [
            'occupiedBy' => $occupiedBy,
            'occupancy' => $roster->occupancyOf($this->dormitory),
            'away' => $roster->awayFrom($this->dormitory),
            'onDuty' => $this->dormitory->supervisions()->onDuty()->with('user')->get(),
            'canManage' => $canManage,
            'assignableBeds' => $assignableBeds,
            'learners' => $canManage && $this->dormitory->is_active && $assignableBeds->isNotEmpty() ? $this->attendingLearners() : collect(),
        ]);
    }

    private function authorizeManage(): void
    {
        Gate::authorize('update', $this->dormitory);
    }

    private function room(int $roomId): DormitoryRoom
    {
        return $this->dormitory->rooms()->findOrFail($roomId);
    }

    private function bed(int $bedId): DormitoryBed
    {
        return DormitoryBed::query()
            ->whereHas('room', fn ($room) => $room->where('dormitory_id', $this->dormitory->id))
            ->findOrFail($bedId);
    }
}
