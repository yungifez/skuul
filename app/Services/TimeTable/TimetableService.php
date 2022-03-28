<?php

namespace App\Services\TimeTable;

use App\Models\Timetable;

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
}
