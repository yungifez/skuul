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

    public function index(): View
    {
        return view('pages.timetable.custom-timetable-item.index');
    }

    public function create(): View
    {
        return view('pages.timetable.custom-timetable-item.create');
    }

    public function show(CustomTimetableItem $customTimetableItem): Response
    {
        return abort(404);
    }

    public function edit(CustomTimetableItem $customTimetableItem)
    {
        return view('pages.timetable.custom-timetable-item.edit', compact('customTimetableItem'));
    }
}
