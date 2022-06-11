<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeTimetableRecord;
use App\Http\Requests\StoreTimetableTimeSlotRequest;
use App\Http\Requests\UpdateTimetableTimeSlotRequest;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Services\Timetable\TimeslotService;

class TimetableTimeSlotController extends Controller
{
    public function __construct(TimeslotService $timeSlot)
    {
        $this->timeSlot = $timeSlot;
        $this->authorizeResource(TimetableTimeSlot::class, 'time_slot');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreTimetableTimeSlotRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimetableTimeSlotRequest $request, Timetable $timetable)
    {
        $data = $request->except('_token');
        $this->timeSlot->createTimeSlot($timetable, $data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\TimetableTimeSlot $timetableTimeSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function show(TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\TimetableTimeSlot $timetableTimeSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateTimetableTimeSlotRequest $request
     * @param \App\Models\TimetableTimeSlot                     $timetableTimeSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimetableTimeSlotRequest $request, $timetable, TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Timetable         $timetable
     * @param TimetableTimeSlot $timeSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timetable $timetable, TimetableTimeSlot $timeSlot)
    {
        $this->timeSlot->deleteTimeSlot($timeSlot);

        return back();
    }

    //timetable record
    public function addTimetableRecord(Timetable $timetable, TimetableTimeSlot $timeSlot, storeTimetableRecord $request)
    {
        $this->authorize('update', $timetable);
        $data = $request->except('_token');
        $this->timeSlot->createTimetableRecord($timeSlot, $data);

        return back();
    }
}
