<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDormitoryRequest;
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

    public function __construct()
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

            return $dormitory;
        });

        return redirect()
            ->route('dormitories.show', $dormitory->id)
            ->with('success', "$dormitory->name is open.");
    }

    /**
     * Show one house: who sleeps where, who is out, and who is on duty.
     */
    public function show(Dormitory $dormitory, BoardingRoster $roster): View
    {
        $dormitory->loadMissing(['rooms.beds']);

        $places = $roster->inDormitory($dormitory);

        return view('pages.boarding.show', [
            'dormitory' => $dormitory,
            'places' => $places,
            'occupiedBy' => $places->keyBy('dormitory_bed_id'),
            'away' => $roster->awayFrom($dormitory),
            'occupancy' => $roster->occupancyOf($dormitory),
            'onDuty' => $dormitory->supervisions()->onDuty()->with('user')->get(),
            'learners' => $this->schoolLearners(),
            'canManage' => auth()->user()?->can('manage boarding') === true,
        ]);
    }
}
