<?php

namespace App\Services\Syllabus;

use App\Models\Syllabus;
use Illuminate\Support\Facades\Storage;

class SyllabusService
{
    // get all syllabus in academic period and class
    public function getAllSyllabiInAcademicPeriodAndClass($academic_period_id, $class_id)
    {
        return Syllabus::where('academic_period_id', $academic_period_id)->get()->load('subject', 'subject.myClass')->filter(function (Syllabus $syllabus) use ($class_id) {
            return $syllabus->subject->myClass->id == $class_id;
        });
    }

    public function getSyllabusById($id)
    {
        return Syllabus::find($id);
    }

    public function createSyllabus($data)
    {
        $data['academic_period_id'] = current_academic_period_id();

        $data['file'] = $data['file']->store(
            'syllabus/',
            'public'
        );

        Syllabus::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'file' => $data['file'],
            'subject_id' => $data['subject_id'],
            'academic_period_id' => $data['academic_period_id'],
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
