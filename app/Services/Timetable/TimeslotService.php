<?php

namespace App\Services\Timetable;

use App\Models\TimetableTimeSlot;
use Illuminate\Support\Facades\DB;

class TimeSlotService
{
    public function createTimeSlot($timetable, $data)
    {
        DB::transaction(function () use ($data, $timetable) {
            $data['timetable_id'] = $timetable->id;
            TimetableTimeSlot::create([
                'start_time'   => $data['start_time'],
                'stop_time'    => $data['stop_time'],
                'timetable_id' => $data['timetable_id'],
            ]);
        });

        return session()->flash('success', 'Time slot successfully created');
    }

    public function deleteTimeSlot(TimetableTimeSlot $timeSlot)
    {
        $timeSlot->delete();

        return session()->flash('success', __('Time slot deleted successfully'));
    }

    //create timetable record
    public function createTimetableRecord(TimetableTimeSlot $timeSlot, $data)
    {
        //remove existing record
        if ($timeSlot->weekdays->find($data['weekday_id']) || $data['subject_id'] == null) {
            $timeSlot->weekdays()->detach($data['weekday_id']);
        }
        if ($data['subject_id'] != null) {
            $timeSlot->weekdays()->attach($data['weekday_id'], ['subject_id' => $data['subject_id']]);
        }

        return session()->flash('success', __('Timetable record successfully created'));
    }
}
