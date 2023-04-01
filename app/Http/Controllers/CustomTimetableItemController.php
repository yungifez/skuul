<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomTimetableItemRequest;
use App\Http\Requests\UpdateCustomTimetableItemRequest;
use App\Models\CustomTimetableItem;
use App\Services\Timetable\TimetableService;
use Illuminate\Http\RedirectResponse;
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
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomTimetableItemRequest $request): RedirectResponse
    {
        $this->timetableService->createCustomTimetableItem($request->validated());

        return back()->with('success', 'Custom timetable item created successfully');
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomTimetableItemRequest $request, CustomTimetableItem $customTimetableItem): RedirectResponse
    {
        $this->timetableService->updateCustomTimetableItem($customTimetableItem, $request->validated());

        return back()->with('success', 'Custom timetable item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomTimetableItem $customTimetableItem): RedirectResponse
    {
        $this->timetableService->deleteCustomTimetableItem($customTimetableItem);

        return back()->with('success', 'Custom timetable item deleted successfully');
    }
}
