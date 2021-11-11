<?php

namespace App\Services\School;

use App\Models\School;
use Illuminate\Support\Str;
use App\Services\School\UserService;

class SchoolService
{
    public $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

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

    public function updateSchool($id, $records)
    {
        $school = $this->getSchoolById($id);
        $school->update($records);

        return $school;
    }

    public function setSchool($id)
    {
        $school = $this->getSchoolById($id);

        if ($school->exists()) {
            $user = $this->userService->getUserById(auth()->user()->id);
            $user->school_id = $school->id;
            $user->save();

            return true;
        }

        return false;
    }

    public function generateSchoolCode(){
        return Str::random(10);
    }
    
}