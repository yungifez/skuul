<?php

namespace App\Services\AcademicYear;

use App\Models\AcademicYear;
use App\Services\School\SchoolService;

class AcademicYearService
{
    public $school;

    public function __construct(SchoolService $school)
    {
        $this->school = $school;
    }

    public function getAllAcademicYears()
    {
        return AcademicYear::where('school_id', auth()->user()->school_id)->get();
    }

    public function getAcademicYearById($id)
    {
        return AcademicYear::where('id', $id)->first();
    }

    public function createAcademicYear($records)
    {
        $records['school_id'] = auth()->user()->school_id;
        AcademicYear::create($records);

        return session()->flash('success', 'Academic year created successfully');
    }

    public function setAcademicYear($academicYearId, $schoolId = null)
    {
        if (!isset($schoolId)) {
            $schoolId = auth()->user()->school_id;
        }
        $school = $this->school->getSchoolById($schoolId);
        $school->academic_year_id = $academicYearId;
        //set semester id to null
        $school->semester_id = null;
        $school->save();
        
        return session()->flash('success', "Academic year set for {$school->name} successfully");
    }
}
