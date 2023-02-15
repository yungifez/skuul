<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Response;
use App\Models\CustomTimetableItem;
use Illuminate\Http\RedirectResponse;
use App\Services\Timetable\TimetableService;
use App\Http\Requests\StoreCustomTimetableItemRequest;
use App\Http\Requests\UpdateCustomTimetableItemRequest;

class CustomTimetableItemController extends Controller
{
    /**
     * Instance of timetable service.
     *
     * @var TimetableService
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
     *
     * @param \App\Http\Requests\StoreCustomTimetableItemRequest $request
     */
    public function store(StoreCustomTimetableItemRequest $request): RedirectResponse
    {
        $this->timetableService->createCustomTimetableItem($request->validated());

        return back()->with('success', 'Custom timetable item created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CustomTimetableItem $customTimetableItem
     */
    public function show(CustomTimetableItem $customTimetableItem): Response 
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CustomTimetableItem $customTimetableItem
     */
    public function edit(CustomTimetableItem $customTimetableItem)
    {
        $data['customTimetableItem'] = $customTimetableItem;

        return view('pages.timetable.custom-timetable-item.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateCustomTimetableItemRequest $request
     * @param \App\Models\CustomTimetableItem                     $customTimetableItem
     */
    public function update(UpdateCustomTimetableItemRequest $request, CustomTimetableItem $customTimetableItem): RedirectResponse
    {
        $data = $request->validated();
        $this->timetableService->updateCustomTimetableItem($customTimetableItem, $data);

        return back()->with('success', 'Custom timetable item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CustomTimetableItem $customTimetableItem
     */
    public function destroy(CustomTimetableItem $customTimetableItem): RedirectResponse
    {
        $this->timetableService->deleteCustomTimetableItem($customTimetableItem);

        return back()->with('success', 'Custom timetable item deleted successfully');
    }
}
