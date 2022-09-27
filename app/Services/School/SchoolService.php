<?php

namespace App\Services\School;

use App\Models\School;
use App\Services\User\UserService;
use Illuminate\Support\Str;

class SchoolService
{
    /**
     * @var UserService
     */
    public $user;

    /**
     * User service constructor.
     *
     * @param UserService $user
     */
    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Get all schools.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSchools()
    {
        return School::all();
    }

    /**
     * Get a school by id.
     *
     * @param int $id
     *
     * @return \App\Models\School
     */
    public function getSchoolById($id)
    {
        return School::find($id);
    }

    /**
     * Create school.
     *
     * @param array $record
     *
     * @return App\Models\School
     */
    public function createSchool($record)
    {
        $record['code'] = $this->generateSchoolCode();
        $school = School::create($record);
        session()->flash('success', __('School created successfully'));
    }

    /**
     * Update school.
     *
     * @param School $school
     * @param $records
     *
     * @return App\Models\School
     */
    public function updateSchool(School $school, $records)
    {
        $school->name = $records['name'];
        $school->address = $records['address'];
        $school->initials = $records['initials'];
        $school->phone = $records['phone'];
        $school->email = $records['email'];
        $school->save();
        session()->flash('success', __('School updated successfully'));
    }

    /**
     * Set authenticated user's school.
     *
     * @param int $id
     *
     * @return void
     */
    public function setSchool($id)
    {
        $school = $this->getSchoolById($id);

        if ($school->exists()) {
            auth()->user()->school_id = $school->id;
            auth()->user()->save();
            session()->flash('success', __('School set successfully'));

            return;
        }
        session()->flash('danger', __('School not found'));
    }

    /**
     * Generate school code.
     *
     * @return string
     */
    public function generateSchoolCode()
    {
        return Str::random(10);
    }

    /**
     * Delete school.
     *
     * @param School $school
     *
     * @return void
     */
    public function deleteSchool(School $school)
    {
        if ($school->users->count('id')) {
            session()->flash('danger', __('Remove all users from this school and make sure school is not set for any super admin'));

            return;
        }
        $school->delete();
        session()->flash('success', __('School deleted successfully'));
    }
}
