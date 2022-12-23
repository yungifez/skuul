<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomTimetableItemRequest;
use App\Http\Requests\UpdateCustomTimetableItemRequest;
use App\Models\CustomTimetableItem;
use App\Services\Timetable\TimetableService;

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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.timetable.custom-timetable-item.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.timetable.custom-timetable-item.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreCustomTimetableItemRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomTimetableItemRequest $request)
    {
        $this->timetableService->createCustomTimetableItem($request->validated());

        return back()->with('success', 'Custom timetable item created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CustomTimetableItem $customTimetableItem
     *
     * @return \Illuminate\Http\Response
     */
    public function show(CustomTimetableItem $customTimetableItem)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CustomTimetableItem $customTimetableItem
     *
     * @return \Illuminate\Http\Response
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
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomTimetableItemRequest $request, CustomTimetableItem $customTimetableItem)
    {
        $data = $request->validated();
        $this->timetableService->updateCustomTimetableItem($customTimetableItem, $data);

        return back()->with('success', 'Custom timetable item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CustomTimetableItem $customTimetableItem
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomTimetableItem $customTimetableItem)
    {
        $this->timetableService->deleteCustomTimetableItem($customTimetableItem);

        return back()->with('success', 'Custom timetable item deleted successfully');
    }
}
