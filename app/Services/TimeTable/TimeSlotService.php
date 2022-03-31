<?php

namespace App\Services\Timetable;

use App\Models\TimetableTimeSlot;
use Illuminate\Support\Facades\DB;

class TimeSlotService
{
    public function createTimeSlot($timetable,$data)
    {
        DB::transaction(function () use ($data,$timetable){
            $data['timetable_id'] = $timetable->id;
            TimetableTimeSlot::create([
                'start_time' => $data['start_time'],
                'stop_time' => $data['stop_time'],
                'timetable_id' => $data['timetable_id']
            ]);
        });

        return session()->flash('success', 'Time slot successfully created');
    }
}