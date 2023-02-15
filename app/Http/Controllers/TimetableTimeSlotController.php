<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Response;
use App\Models\TimetableTimeSlot;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\storeTimetableRecord;
use App\Services\Timetable\TimeSlotService;
use App\Http\Requests\StoreTimetableTimeSlotRequest;
use App\Http\Requests\UpdateTimetableTimeSlotRequest;

class TimetableTimeSlotController extends Controller
{
    public $timeSlot;

    public function __construct(TimeSlotService $timeSlot)
    {
        $this->timeSlot = $timeSlot;
        $this->authorizeResource(TimetableTimeSlot::class, 'time_slot');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): Response
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreTimetableTimeSlotRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimetableTimeSlotRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $this->timeSlot->createTimeSlot($data);

        return back()->with('success', 'Time slot successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\TimetableTimeSlot $timetableTimeSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function show(TimetableTimeSlot $timetableTimeSlot): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\TimetableTimeSlot $timetableTimeSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(TimetableTimeSlot $timetableTimeSlot): Response
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateTimetableTimeSlotRequest $request
     * @param \App\Models\TimetableTimeSlot                     $timetableTimeSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimetableTimeSlotRequest $request, $timetable, TimetableTimeSlot $timetableTimeSlot): Response
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Timetable         $timetable
     * @param TimetableTimeSlot $timeSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimetableTimeSlot $timeSlot): RedirectResponse
    {
        $this->timeSlot->deleteTimeSlot($timeSlot);

        return back()->with('success', __('Time slot deleted successfully'));
    }

    //timetable record
    public function addTimetableRecord(TimetableTimeSlot $timeSlot, storeTimetableRecord $request): RedirectResponse
    {
        $timetable = $timeSlot->timetable;
        $this->authorize('update', $timetable);
        $data = $request->except('_token');
        $this->timeSlot->createTimetableRecord($timeSlot, $data);

        return back()->with('success', __('Timetable record successfully created'));
    }
}
