<?php

namespace App\Services\School;

use App\Enums\PlatformPermission;
use App\Exceptions\ResourceNotEmptyException;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Collection;
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
    public function __construct(UserService $user, private SystemPermissionScope $systemPermissionScope)
    {
        $this->user = $user;
    }

    /**
     * Get all schools.
     *
     * @return Collection
     */
    public function getAllSchools()
    {
        return School::all();
    }

    /**
     * Get the schools a person may open.
     *
     * A platform administrator may open any school. Everyone else sees only
     * the schools they hold an active membership in.
     */
    public function getSchoolsForUser(?User $user = null): Collection
    {
        $user ??= auth()->user();

        if ($user === null) {
            return School::query()->whereRaw('1 = 0')->get();
        }

        return $this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools)
            ? School::orderBy('name')->get()
            : $user->schools()->orderBy('name')->get();
    }

    /**
     * Get a school by id.
     *
     * @param  int  $id
     * @return School
     */
    public function getSchoolById($id)
    {
        return School::find($id);
    }

    /**
     * Create school.
     *
     * @param  array  $record
     * @return School
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
     * @return School
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
     * @return void
     */
    public function setSchool(School $school)
    {
        $user = auth()->user();

        if (!$this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools) && !$user->belongsToSchool($school)) {
            abort(403, 'You do not have access to that school.');
        }

        app(SchoolContext::class)->set($school);
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
        if ($school->users->isNotEmpty()) {
            throw new ResourceNotEmptyException('Remove all users from this school and make sure school is not set for any super admin');

            return;
        }
        $school->delete();
    }
}
