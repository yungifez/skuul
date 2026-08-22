<?php

namespace App\Services\Authorization;

use App\Enums\OrganizationPermission;
use App\Enums\SchoolMembershipStatus;
use App\Models\CampusMoveRequest;
use App\Models\School;
use App\Models\User;
use Closure;
use Illuminate\Support\Arr;
use Spatie\Permission\PermissionRegistrar;

/**
 * Answer who may move a student between campuses, and who may only ask.
 *
 * Moving a student is tiered. A person with organization authority moves them
 * straight away, because the organization owns both campuses. A campus
 * administrator only asks: the campus that receives the student decides.
 *
 * A campus permission is held per school through Spatie's team scope, so a
 * check against another campus has to name that campus first.
 */
class CampusMoveAuthority
{
    /**
     * The permission a campus administrator needs to ask for a move.
     */
    public const RequestPermission = 'request campus move';

    /**
     * The permission the receiving campus needs to decide.
     */
    public const ApprovePermission = 'approve campus move';

    /**
     * Answers already worked out in this request, keyed by user, school, and permission.
     *
     * @var array<string, bool>
     */
    private array $answers = [];

    public function __construct(
        private OrganizationPermissionScope $organizationPermissionScope,
        private PermissionRegistrar $permissionRegistrar,
    ) {
    }

    /**
     * Check if this person moves a student without asking either campus.
     */
    public function movesFreely(User $user, School $destination): bool
    {
        return $this->organizationPermissionScope->allows(
            $user,
            $destination->organization_id,
            OrganizationPermission::MoveStudents,
        );
    }

    /**
     * Check if this person may ask the other campus to take a student.
     */
    public function canRequest(User $user, School $source): bool
    {
        return $this->allowsInSchool($user, $source, self::RequestPermission);
    }

    /**
     * Check if this person decides the moves arriving at one campus.
     *
     * This answers the screen, which asks before it has a single request.
     */
    public function canDecideAtCampus(User $user, School $campus): bool
    {
        return $this->movesFreely($user, $campus)
            || $this->allowsInSchool($user, $campus, self::ApprovePermission);
    }

    /**
     * Check if this person may approve or reject the request.
     *
     * The receiving campus decides. A person with organization authority may
     * decide too, because they could have made the move without asking.
     */
    public function canDecide(User $user, CampusMoveRequest $request): bool
    {
        return $this->canDecideAtCampus($user, $request->toSchool);
    }

    /**
     * Check if this person may take the request back.
     *
     * Only the campus that asked withdraws its own request.
     */
    public function canCancel(User $user, CampusMoveRequest $request): bool
    {
        if ($this->movesFreely($user, $request->toSchool)) {
            return true;
        }

        return $this->allowsInSchool($user, $request->fromSchool, self::RequestPermission);
    }

    /**
     * Check one school-scoped permission against a named campus.
     *
     * The person must be able to work in that campus at all, so an ended
     * membership never carries a permission.
     */
    private function allowsInSchool(User $user, School $school, string $permission): bool
    {
        $key = $user->getAuthIdentifier().'|'.$school->id.'|'.$permission;

        if (array_key_exists($key, $this->answers)) {
            return $this->answers[$key];
        }

        // The working school already has its roles and permissions loaded, so
        // asking about it costs nothing. Reloading them for it would make
        // every screen repeat the permission queries.
        if ($school->id === current_school_id()) {
            return $this->answers[$key] = $user->can($permission);
        }

        $isMember = $user->schoolMemberships()
            ->where('school_id', $school->id)
            ->where('status', SchoolMembershipStatus::Active)
            ->exists();

        if (!$isMember) {
            return $this->answers[$key] = false;
        }

        return $this->answers[$key] = $this->withinSchool($user, $school, fn (): bool => $user->can($permission));
    }

    /**
     * Forget answers cached during this request.
     */
    public function forget(): void
    {
        $this->answers = [];
    }

    /**
     * Run one permission check against the team of a named campus.
     *
     * Roles and direct permissions are already loaded for the working school,
     * so they are dropped and put back; otherwise the check reads the wrong
     * campus and quietly answers for the school the person is working in.
     *
     * @template T
     *
     * @param Closure(): T $callback
     *
     * @return T
     */
    private function withinSchool(User $user, School $school, Closure $callback): mixed
    {
        $previousTeamId = $this->permissionRegistrar->getPermissionsTeamId();
        $workingRelations = Arr::only($user->getRelations(), ['roles', 'permissions']);

        $user->unsetRelation('roles')->unsetRelation('permissions');

        try {
            $this->permissionRegistrar->setPermissionsTeamId($school->id);

            return $callback();
        } finally {
            $this->permissionRegistrar->setPermissionsTeamId($previousTeamId);
            $user->unsetRelation('roles')->unsetRelation('permissions');

            foreach ($workingRelations as $relation => $value) {
                $user->setRelation($relation, $value);
            }
        }
    }
}
