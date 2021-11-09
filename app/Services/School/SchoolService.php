<?php

namespace App\Services\School;

use App\Models\School;
use Illuminate\Support\Str;

class SchoolService
{
    public function getAllSchools()
    {
       return School::all();
    }

    public function getSchoolById($id)
    {
        return School::find($id);
    }

    public function createSchool($records)
    {
        return School::create($records);
    }

    public function setCurrentSchool($id)
    {
        $school = $this->getSchoolById();

        if ($school->exists) {
            auth()->user()->school_id = $school->id;

            return 1;
        }

        return 0;
    }

    public function generateSchoolCode(){
        return Str::random(10);
    }
}