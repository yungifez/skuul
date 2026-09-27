<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use App\Services\Boarding\BoardingRoster;
use Illuminate\View\View;

/**
 * The boarding houses of one campus, and who sleeps in them.
 */
class DormitoryController extends Controller
{
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
     * Show the form for changing a house.
     */
    public function edit(Dormitory $dormitory): View
    {
        return view('pages.boarding.edit', ['dormitory' => $dormitory]);
    }

    /**
     * Show one house: who sleeps where, who is out, and who is on duty.
     */
    public function show(Dormitory $dormitory): View
    {
        return view('pages.boarding.show', [
            'dormitory' => $dormitory,
            'canManage' => auth()->user()?->can('manage boarding') === true,
        ]);
    }
}
