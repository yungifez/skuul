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
    //get semesters by 
    public function getAllSemestersInAcademicYear($academicYear)
    {
        return $this->getAllSemesters()->where('academic_year_id', $academicYear);
    }
    public function createSemester($data)
    {
        $data['academic_year_id'] = auth()->user()->school->academicYear->id;
        $data['school_id'] = auth()->user()->school->id;
        Semester::create([
            'name' => $data['name'],
            'school_id' => $data['school_id'],
            'academic_year_id' => $data['academic_year_id']
        ]);

        return session()->flash('success',"Successfully created semester");
    }
}
