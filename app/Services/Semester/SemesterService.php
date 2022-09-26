<?php

namespace App\Services\Semester;

use App\Models\Semester;

class SemesterService
{
    //get all semesters
    public function getAllSemesters()
    {
        return Semester::where(['school_id'=> auth()->user()->school_id])->get();
    }

    //get semesters by academic year
    public function getAllSemestersInAcademicYear($academicYear)
    {
        return $this->getAllSemesters()->where('academic_year_id', $academicYear);
    }

    //get semester by id
    public function getSemesterById($id)
    {
        return Semester::find($id);
    }

    public function createSemester($data)
    {
        $data['academic_year_id'] = auth()->user()->school->academicYear->id;
        $data['school_id'] = auth()->user()->school->id;
        Semester::create([
            'name'             => $data['name'],
            'school_id'        => $data['school_id'],
            'academic_year_id' => $data['academic_year_id'],
        ]);

        return session()->flash('success', 'Successfully created semester');
    }

    //set semester as current school semester

    public function setSemester($semester)
    {
        $semester = $this->getSemesterById($semester);
        $school = auth()->user()->school;
        if ($semester->academicYear->id != $school->academic_year_id) {
            return session()->flash('error', 'This semester isn\'t in your current academic year');
        }
        $school->semester_id = $semester->id;
        $school->save();

        return session()->flash('success', 'Successfully set current semester');
    }

    //update semester

    public function updateSemester(Semester $semester, $data)
    {
        $semester->name = $data['name'];
        $semester->save();

        return session()->flash('success', 'Successfully updated semester');
    }

    //delete semester

    public function deleteSemester(Semester $semester)
    {
        $semester->delete();

        return session()->flash('success', 'Successfully deleted semester');
    }
}
