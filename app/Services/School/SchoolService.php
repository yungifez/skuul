<?php

namespace App\Services\School;

use App\Exceptions\ResourceNotEmptyException;
use App\Models\School;
use App\Services\User\UserService;
use Illuminate\Support\Str;
use Storage;

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
     * @return \App\Models\School
     */
    public function createSchool($record)
    {
        $record['code'] = $this->generateSchoolCode();

        if (isset($record['logo'])) {
            $record['logo_path'] = Storage::disk('public')->put('schools', $record['logo']);
            unset($record['logo']);
        }

        $school = School::create($record);

        return $school;
    }

    /**
     * Update school.
     *
     * @return \App\Models\School
     */
    public function updateSchool(School $school, $record)
    {
        $school->name = $record['name'];
        $school->address = $record['address'];
        $school->initials = $record['initials'];
        $school->phone = $record['phone'];
        $school->email = $record['email'];

        if (isset($record['logo'])) {
            $school->logo_path = Storage::disk('public')->put('schools', $record['logo']);
        }

        $school->save();

        return $school;
    }

    /**
     * Set authenticated user's school.
     *
     * @param \App\Models\School
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
