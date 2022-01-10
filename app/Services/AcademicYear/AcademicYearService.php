<?php

namespace App\Services\AcademicYear;

use App\Models\AcademicYear;

class AcademicYearService
{
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

}