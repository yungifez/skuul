<?php

namespace App\Services\School;

use App\Models\School;
use App\Services\User\UserService;
use Illuminate\Support\Str;

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
        return School::with('myClasses')->find($id);
    }

    public function createSchool($records)
    {
        $records['code'] = $this->generateSchoolCode();
        $school = School::create($records);
        session()->flash('success', __('School created successfully'));

        return $school;
    }

    public function updateSchool(School $school, $records)
    {
        $school->update($records);
        session()->flash('success', __('School updated successfully'));

        return $school;
    }

    public function setSchool($id)
    {
        $school = $this->getSchoolById($id);

        if ($school->exists()) {
            $user = $this->userService->getUserById(auth()->user()->id);
            $user->school_id = $school->id;
            $user->save();
            session()->flash('success', __('School set successfully'));

            return true;
        }

        session()->flash('danger', __('School not found'));

        return false;
    }

    public function generateSchoolCode()
    {
        return Str::random(10);
    }

    // delete school

    public function deleteSchool(School $school)
    {
        if ($school->users->count('id')) {
            return session()->flash('danger', __('Remove all users from this school and make sure school is not set for any super admin'));
        }
        $school->delete();
    
        return session()->flash('success', __('School deleted successfully'));;
    }
}
