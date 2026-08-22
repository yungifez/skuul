<?php

namespace App\Policies;

use App\Models\AccountInvitation;
use App\Models\User;
use App\Services\Identity\AccountInvitationVisibility;

/**
 * Who may read and act on account invitations.
 *
 * Reading is limited by school reach: see
 * App\Services\Identity\AccountInvitationVisibility. Acting adds two rules on
 * top. Nobody may work on their own invitation, and an invitation that was
 * accepted, revoked, or that expired is finished, so it cannot be resent or
 * revoked again.
 */
class AccountInvitationPolicy
{
    public function __construct(private AccountInvitationVisibility $visibility)
    {
    }

    /**
     * Determine whether the user can open the invitation screen.
     */
    public function viewAny(User $user): bool
    {
        return $this->visibility->allowsAny($user);
    }

    /**
     * Determine whether the user can read one invitation.
     */
    public function view(User $user, AccountInvitation $invitation): bool
    {
        return $this->visibility->allows($user, $invitation);
    }

    /**
     * Determine whether the user can email the link again.
     */
    public function resend(User $user, AccountInvitation $invitation): bool
    {
        return $invitation->isPending() && $this->canAct($user, $invitation);
    }

    /**
     * Determine whether the user can stop the link.
     */
    public function revoke(User $user, AccountInvitation $invitation): bool
    {
        return $invitation->isPending() && $this->canAct($user, $invitation);
    }

    /**
     * Check the rules both actions share.
     *
     * Account access is never changed by the person who holds the account,
     * which matches App\Policies\UserPolicy::manageAccountAccess.
     */
    private function canAct(User $user, AccountInvitation $invitation): bool
    {
        return $user->id !== $invitation->user_id
            && $this->visibility->allows($user, $invitation);
    }
}
