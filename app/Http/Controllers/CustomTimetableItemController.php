<?php

namespace App\Http\Controllers;

use App\Models\CustomTimetableItem;
use App\Services\Timetable\TimetableService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CustomTimetableItemController extends Controller
{
    /**
     * Instance of timetable service.
     */
    public TimetableService $timetableService;

    public function __construct(TimetableService $timetableService)
    {
        $this->timetableService = $timetableService;
        $this->authorizeResource(CustomTimetableItem::class, 'custom_timetable_item');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.timetable.custom-timetable-item.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.timetable.custom-timetable-item.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomTimetableItem $customTimetableItem): Response
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomTimetableItem $customTimetableItem)
    {
        return view('pages.timetable.custom-timetable-item.edit', compact('customTimetableItem'));
    }
}
