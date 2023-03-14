<?php

namespace App\Services\School;

use App\Exceptions\ResourceNotEmptyException;
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

        return $school;
    }

    /**
     * Update school.
     *
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

        return $school;
    }

    /**
     * Set authenticated user's school.
     *
     * @param int $id
     *
     * @return void
     */
    public function setSchool(School $school)
    {
        auth()->user()->school_id = $school->id;
        auth()->user()->save();
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
     *
     * @return void
     */
    public function deleteSchool(School $school)
    {
        if ($school->users->count('id')) {
            throw new ResourceNotEmptyException('Remove all users from this school and make sure school is not set for any super admin');

            return;
        }
        $school->delete();
    }
}
