<?php

namespace App\Http\Controllers;

use App\Actions\Boarding\RecordBoardingRoll;
use App\Actions\Boarding\StartBoardingRoll;
use App\Enums\BoardingRollEntryStatus;
use App\Enums\BoardingRollType;
use App\Http\Requests\StoreBoardingRollRequest;
use App\Http\Requests\UpdateBoardingRollRequest;
use App\Models\BoardingRoll;
use App\Models\Dormitory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * The daily accountability checks for boarding houses.
 */
class BoardingRollController extends Controller
{
    /**
     * Show the roll status for every active house.
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('read boarding'), 403);

        $date = $request->date('taken_on') ?? Carbon::today();
        $houses = Dormitory::inSchool()->active()->orderBy('name')->get();
        $rolls = BoardingRoll::inSchool()
            ->onDate($date->toDateString())
            ->with('entries:id,boarding_roll_id,status')
            ->get()
            ->groupBy('dormitory_id');

        return view('pages.boarding.rolls.index', [
            'houses' => $houses,
            'rolls' => $rolls,
            'date' => $date,
            'types' => BoardingRollType::cases(),
            'canManage' => $request->user()->can('manage boarding'),
        ]);
    }

    /**
     * Start one house roll.
     */
    public function store(StoreBoardingRollRequest $request, StartBoardingRoll $start): RedirectResponse
    {
        $house = Dormitory::inSchool()->findOrFail($request->integer('dormitory_id'));
        $roll = $start->start($house, BoardingRollType::from($request->validated('type')), $request->validated('taken_on'), $request->user());

        return redirect()->route('boarding-rolls.show', $roll)->with('success', 'The boarding roll is ready to take.');
    }

    /**
     * Show one roll.
     */
    public function show(Request $request, BoardingRoll $boardingRoll): View
    {
        abort_unless($request->user()?->can('read boarding'), 403);
        abort_unless($boardingRoll->school_id === current_school_id(), 404);

        $boardingRoll->load(['dormitory', 'entries.studentRecord.user']);

        return view('pages.boarding.rolls.show', [
            'roll' => $boardingRoll,
            'statuses' => BoardingRollEntryStatus::cases(),
            'canManage' => $request->user()->can('manage boarding'),
        ]);
    }

    /**
     * Save answers for one roll.
     */
    public function update(UpdateBoardingRollRequest $request, BoardingRoll $boardingRoll, RecordBoardingRoll $record): RedirectResponse
    {
        $record->record($boardingRoll, $request->validated('entries'), $request->boolean('complete'), $request->user());

        return redirect()->route('boarding-rolls.show', $boardingRoll)->with('success', $request->boolean('complete') ? 'The boarding roll is complete.' : 'The boarding roll was saved.');
    }
}
