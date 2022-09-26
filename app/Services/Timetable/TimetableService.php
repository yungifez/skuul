<?php

namespace App\Services\Timetable;

use App\Models\Timetable;
use App\Services\Print\PrintService;
use Illuminate\Support\Facades\DB;

class TimetableService
{
    //get all syllabus in semester and class
    public function getAllTimetablesInSemesterAndClass($semester_id, $class_id)
    {
        return Timetable::where('semester_id', $semester_id)->get()->filter(function ($timetable) use ($class_id) {
            return $timetable->my_class_id == $class_id;
        });
    }

    //create timetable

    public function createTimetable($data)
    {
        DB::transaction(function () use ($data) {
            $data['semester_id'] = auth()->user()->school->semester_id;
            if (!isset($data['description'])) {
                $data['description'] = null;
            }
            Timetable::create([
                'name'        => $data['name'],
                'description' => $data['description'],
                'my_class_id' => $data['my_class_id'],
                'semester_id' => $data['semester_id'],
            ]);
        });

        return session()->flash('success', 'Timetable created successfully');
    }

    //update timetable

    public function updateTimetable(Timetable $timetable, $data)
    {
        DB::transaction(function () use ($data, $timetable) {
            $timetable->name = $data['name'];
            $timetable->description = $data['description'];
            $timetable->save();
        });

        return session()->flash('success', 'Timetable updated successfully');
    }

    // print timetable
    public function printTimetable(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView($name, $view, $data)->download();
    }

    //delete timetable

    public function deleteTimetable(Timetable $timetable)
    {
        $timetable->delete();

        return session()->flash('success', 'Timetable deleted successfully');
    }
}
