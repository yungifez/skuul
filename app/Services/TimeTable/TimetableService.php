<?php

namespace App\Services\TimeTable;

use App\Models\Timetable;
use Illuminate\Support\Facades\DB;

class TimetableService
{
    //get all syllabus in semester and class
    public function getAllTimetablesInSemesterAndClass($semester_id, $class_id)
    {
        return Timetable::where('semester_id', $semester_id)->get()->filter(function ($timetable) use ($class_id)
        {
            return $timetable->subject->myClass->id == $class_id;
        });
    }

    //create timetable

    public function createTimetable($data)
    {
        DB::transaction(function() use ($data) {
            $data['semester_id'] = auth()->user()->school->semester_id;

            if(!isset($data['description'])) {
                $data['description'] = null;
            }
            Timetable::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'subject_id' => $data['subject_id'],
                'semester_id' => $data['semester_id'],
            ]);
        });
    }
}