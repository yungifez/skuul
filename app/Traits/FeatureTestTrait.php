<?php

namespace App\Traits;

use App\Actions\School\GrantSchoolMembership;
use App\Models\School;
use App\Models\User;
use App\Services\School\SchoolContext;

trait FeatureTestTrait
{
    /**
     * Create a user with no permissions in the working school.
     */
    public function unauthorized_user(?School $school = null): object
    {
        return $this->actingAsMemberOf($this->workingSchool($school));
    }

    /**
     * Create a user who holds the given permissions in the working school.
     *
     * @param array<int, string> $permissions
     */
    public function authorized_user(array $permissions, ?School $school = null): object
    {
        $school = $this->workingSchool($school);
        $user = $this->memberOf($school);

        // Permissions are school-scoped, so name the school before granting.
        school_context()->set($school, remember: false);
        $user->givePermissionTo($permissions);

        return $this->actingAsMemberOf($school, $user);
    }

    /**
     * Create a person who administers the whole platform.
     */
    public function platform_admin(?School $school = null): object
    {
        $school = $this->workingSchool($school);
        $user = $this->memberOf($school, User::factory()->platformAdmin()->create());

        return $this->actingAsMemberOf($school, $user);
    }

    /**
     * Create a user with an active membership in the given school.
     */
    public function memberOf(School $school, ?User $user = null): User
    {
        $user ??= User::factory()->create();

        app(GrantSchoolMembership::class)->grant($user, $school, primary: true);

        return $user->refresh();
    }

    /**
     * Sign in as a member of the given school and make it the working school.
     */
    public function actingAsMemberOf(School $school, ?User $user = null): object
    {
        $user = $this->memberOf($school, $user);

        school_context()->set($school, remember: false);

        return $this->actingAs($user)->withSession([SchoolContext::SESSION_KEY => $school->id]);
    }

    /**
     * Create a person who holds no school access at all.
     */
    public function nonMember(): User
    {
        $user = User::factory()->create();
        $user->schoolMemberships()->delete();

        return $user->refresh();
    }

    /**
     * Get the school the tests work in, creating it when needed.
     */
    public function workingSchool(?School $school = null): School
    {
        return $school ?? School::first() ?? School::factory()->create();
    }
}
