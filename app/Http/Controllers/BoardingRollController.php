<?php

namespace App\Http\Controllers;

use App\Models\BoardingRoll;
use Illuminate\Http\Request;
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

        return view('pages.boarding.rolls.index');
    }

    /**
     * Show one roll.
     */
    public function show(Request $request, BoardingRoll $boardingRoll): View
    {
        abort_unless($request->user()?->can('read boarding'), 403);
        abort_unless($boardingRoll->school_id === current_school_id(), 404);

        return view('pages.boarding.rolls.show', ['roll' => $boardingRoll->load('dormitory')]);
    }
}
