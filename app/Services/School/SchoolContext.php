<?php

namespace App\Services\School;

use App\Enums\PlatformPermission;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use Illuminate\Http\Request;
use RuntimeException;
use Spatie\Permission\PermissionRegistrar;

/**
 * The school the current request works in.
 *
 * This is the one place that answers "which school am I in right now". It reads
 * from the session, never from the user record, so switching school never
 * writes to the person's profile.
 *
 * Setting the school also sets the Spatie team id, so every role and permission
 * check resolves against the same school.
 */
class SchoolContext
{
    /**
     * The session key that remembers the school between requests.
     */
    public const SESSION_KEY = 'active_school_id';

    private ?School $school = null;

    private bool $resolved = false;

    public function __construct(private SystemPermissionScope $systemPermissionScope)
    {
    }

    /**
     * Get the active school, or null when none is set.
     */
    public function school(): ?School
    {
        return $this->school;
    }

    /**
     * Get the id of the active school, or null when none is set.
     */
    public function id(): ?int
    {
        return $this->school?->id;
    }

    /**
     * Check if a school is set for this request.
     */
    public function has(): bool
    {
        return $this->school !== null;
    }

    /**
     * Get the active school or fail.
     *
     * Use this before a write. A write must always name its school.
     */
    public function schoolOrFail(): School
    {
        if ($this->school === null) {
            throw new RuntimeException('No school is set for this request. Set a working school first.');
        }

        return $this->school;
    }

    /**
     * Set the active school for this request and remember it.
     */
    public function set(?School $school, bool $remember = true): void
    {
        $this->school = $school;
        $this->resolved = true;

        app(PermissionRegistrar::class)->setPermissionsTeamId($school?->id);

        if ($remember && app()->bound('session') && app('session')->isStarted()) {
            $school === null
                ? session()->forget(self::SESSION_KEY)
                : session()->put(self::SESSION_KEY, $school->id);
        }
    }

    /**
     * Clear the active school.
     */
    public function forget(): void
    {
        $this->set(null);
        $this->resolved = false;
    }

    /**
     * Work out which school this request belongs to and set it.
     *
     * The remembered school wins when the person may still use it. Otherwise
     * the person falls back to their primary school, then to any school they
     * can still use.
     */
    public function resolveFor(User $user, ?Request $request = null): ?School
    {
        $remembered = $request?->session()?->get(self::SESSION_KEY);

        $school = $remembered === null
            ? null
            : $this->schoolIfAllowed($user, (int) $remembered);

        $school ??= $this->defaultSchoolFor($user);

        $this->set($school);
        $this->resolved = true;

        return $school;
    }

    /**
     * Check if the request already resolved a school.
     */
    public function isResolved(): bool
    {
        return $this->resolved;
    }

    /**
     * Get the school a person opens by default.
     */
    public function defaultSchoolFor(User $user): ?School
    {
        $membership = $user->schoolMemberships()
            ->active()
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->first();

        if ($membership !== null) {
            return $membership->school;
        }

        return $this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools)
            ? School::orderBy('id')->first()
            : null;
    }

    /**
     * Return the school when the person may work in it, else null.
     */
    public function schoolIfAllowed(User $user, int $schoolId): ?School
    {
        if (!$this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools) && !$user->belongsToSchool($schoolId)) {
            return null;
        }

        return School::find($schoolId);
    }
}
