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
 * Store the working school in the session and align the Spatie team scope.
 * Switching schools does not change the user profile.
 */
class SchoolContext
{
    /**
     * The session key that remembers the school between requests.
     */
    public const SESSION_KEY = 'active_school_id';

    private ?School $school = null;

    private bool $resolved = false;

    public function __construct(
        private SystemPermissionScope $systemPermissionScope,
        private DomainContext $domainContext,
    ) {}

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

        if ($remember && app()->bound('session')) {
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
     * Prefer the domain campus when membership permits it, then the saved or primary school.
     * Fall back to another accessible school.
     */
    public function resolveFor(User $user, ?Request $request = null): ?School
    {
        $school = $this->domainSchoolFor($user);

        if ($school === null) {
            $remembered = $request?->session()?->get(self::SESSION_KEY);

            $school = $remembered === null
                ? null
                : $this->schoolIfAllowed($user, (int) $remembered);
        }

        $school ??= $this->defaultSchoolFor($user);

        $this->set($school);
        $this->resolved = true;

        return $school;
    }

    /**
     * Get the campus the address names, when the person may work in it.
     */
    private function domainSchoolFor(User $user): ?School
    {
        $school = $this->domainContext->school();

        if ($school === null) {
            return null;
        }

        return $this->schoolIfAllowed($user, $school->id);
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
