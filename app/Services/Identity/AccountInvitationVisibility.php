<?php

namespace App\Services\Identity;

use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Enums\SchoolMembershipStatus;
use App\Models\AccountInvitation;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;
use App\Services\Authorization\SystemPermissionScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Answer "which invitations may this person see and act on?".
 *
 * An invitation belongs to a person, and a person can hold membership in
 * several schools. The answer is therefore a set of schools: an administrator
 * sees an invitation when the invited person can work in a school that the
 * administrator is responsible for.
 *
 * A platform administrator sees every school. An organization administrator
 * sees the campuses of the organizations they may manage members in. A school
 * administrator sees the school being worked in, and nothing else.
 */
class AccountInvitationVisibility
{
    public function __construct(
        private SystemPermissionScope $systemPermissionScope,
        private OrganizationPermissionScope $organizationPermissionScope,
    ) {}

    /**
     * Check whether this person may open the invitation screen at all.
     */
    public function allowsAny(User $user): bool
    {
        return $this->schoolIdsFor($user) !== [];
    }

    /**
     * Get the schools whose invitations this person may work with.
     *
     * A null answer means every school, with no condition to apply.
     *
     * @return list<int>|null
     */
    public function schoolIdsFor(User $user): ?array
    {
        if ($this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools)) {
            return null;
        }

        $schoolIds = $this->organizationSchoolIds($user);

        $currentSchoolId = current_school_id();

        if ($currentSchoolId !== null
            && $user->can('manage account access')
            && $user->belongsToSchool($currentSchoolId)) {
            $schoolIds[] = $currentSchoolId;
        }

        return array_values(array_unique($schoolIds));
    }

    /**
     * Start a query that only reads the invitations this person may see.
     *
     * @return Builder<AccountInvitation>
     */
    public function query(User $user): Builder
    {
        $query = AccountInvitation::query();
        $schoolIds = $this->schoolIdsFor($user);

        if ($schoolIds === null) {
            return $query;
        }

        if ($schoolIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas(
            'user.schoolMemberships',
            fn (Builder $membership): Builder => $membership
                ->whereIn('school_id', $schoolIds)
                ->where('status', SchoolMembershipStatus::Active),
        );
    }

    /**
     * Check whether this person may see one invitation.
     */
    public function allows(User $user, AccountInvitation $invitation): bool
    {
        $schoolIds = $this->schoolIdsFor($user);

        if ($schoolIds === null) {
            return true;
        }

        if ($schoolIds === []) {
            return false;
        }

        $invitee = $invitation->user;

        if ($invitee === null) {
            return false;
        }

        return $invitee->schoolMemberships()
            ->active()
            ->whereIn('school_id', $schoolIds)
            ->exists();
    }

    /**
     * Get the names of the invited person's schools that this person may see.
     *
     * Another school's name is never shown, even when the invited person can
     * work there, so the screen cannot leak the reach of one account.
     *
     * @return list<string>
     */
    public function schoolNamesFor(User $user, User $invitee): array
    {
        $schoolIds = $this->schoolIdsFor($user);

        $memberships = $invitee->schoolMemberships
            ->filter(fn ($membership): bool => $membership->status === SchoolMembershipStatus::Active
                && ($schoolIds === null || in_array($membership->school_id, $schoolIds, true)));

        return $memberships
            ->map(fn ($membership): string => $membership->school->name)
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Get the campuses of the organizations this person may manage people in.
     *
     * @return list<int>
     */
    private function organizationSchoolIds(User $user): array
    {
        $organizationIds = $user->organizationMemberships()
            ->active()
            ->pluck('organization_id')
            ->filter(fn (int $organizationId): bool => $this->organizationPermissionScope->allows(
                $user,
                $organizationId,
                OrganizationPermission::ManageMembers,
            ))
            ->all();

        if ($organizationIds === []) {
            return [];
        }

        return School::query()
            ->whereIn('organization_id', $organizationIds)
            ->pluck('id')
            ->all();
    }
}
