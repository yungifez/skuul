<?php

namespace App\Services\Syllabus;

use App\Models\Syllabus;
use Illuminate\Support\Facades\Storage;

class SyllabusService
{
    // get all syllabus in semester and class
    public function getAllSyllabiInSemesterAndClass($semester_id, $class_id)
    {
        return Syllabus::where('semester_id', $semester_id)->get()->load('subject', 'subject.myClass')->filter(function (Syllabus $syllabus) use ($class_id) {
            return $syllabus->subject->myClass->id == $class_id;
        });
    }

    public function getSyllabusById($id)
    {
        return Syllabus::find($id);
    }

    public function createSyllabus($data)
    {
        $data['semester_id'] = current_semester_id();

        $data['file'] = $data['file']->store(
            'syllabus/',
            'public'
        );

        Syllabus::create([
            'name'        => $data['name'],
            'description' => $data['description'],
            'file'        => $data['file'],
            'subject_id'  => $data['subject_id'],
            'semester_id' => $data['semester_id'],
        ]);
    }

    public function updateSyllabus($id, $data)
    {
        return Syllabus::find($id)->update($data);
    }

    public function deleteSyllabus(Syllabus $syllabus)
    {
        Storage::disk('public')->delete($syllabus->file);
        $syllabus->delete();
    }
}
