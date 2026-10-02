<?php

namespace App\Services\School;

use App\Enums\PlatformPermission;
use App\Exceptions\ResourceNotEmptyException;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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

        $school = School::create($record + ['setup_details_completed_at' => now()]);

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
        $school->country = $record['country'];
        $school->state = $record['state'];
        $school->city = $record['city'];
        $school->postal_code = $record['postal_code'];
        if (array_key_exists('timezone', $record)) {
            $school->timezone = $record['timezone'];
        }
        $school->initials = $record['initials'] ?? null;
        $school->phone = $record['phone'] ?? null;
        $school->email = $record['email'] ?? null;
        $school->setup_details_completed_at = now();

        $replacedLogo = null;

        if (isset($record['logo'])) {
            $replacedLogo = $school->logo_path;
            $school->logo_path = Storage::disk('public')->put('schools', $record['logo']);
        }

        $school->save();

        // The old logo has no other reader, so it goes once the new one is in use.
        if ($replacedLogo !== null && $replacedLogo !== $school->logo_path) {
            Storage::disk('public')->delete($replacedLogo);
        }

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
     * Deleting a school removes its books and payments with it. Only a school
     * that never held anybody, any learner or any money may go. One whose
     * people all left still keeps their records.
     *
     * @return void
     */
    public function deleteSchool(School $school)
    {
        if ($school->users->isNotEmpty()) {
            throw new ResourceNotEmptyException('Remove all users from this school and make sure school is not set for any super admin');
        }

        $hasHistory = collect(['school_memberships', 'student_records', 'ledger_transactions'])
            ->contains(fn (string $table): bool => DB::table($table)->where('school_id', $school->id)->exists());

        if ($hasHistory) {
            throw new ResourceNotEmptyException('This school keeps the records of people and money it once held, so it cannot be deleted.');
        }

        $school->delete();
    }
}
