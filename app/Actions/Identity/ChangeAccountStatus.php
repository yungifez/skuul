<?php

namespace App\Actions\Identity;

use App\Enums\AccountStatus;
use App\Enums\PlatformPermission;
use App\Events\AccountStatusChanged;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use RuntimeException;

/**
 * Move an account between access states.
 *
 * The person profile and the school records stay in place. Only access changes.
 */
class ChangeAccountStatus
{
    public function __construct(
        private RevokeAccountInvitation $revokeAccountInvitation,
        private SystemPermissionScope $systemPermissionScope,
    ) {
    }

    /**
     * Stop access without deleting anything.
     */
    public function suspend(User $user, ?User $actor = null, ?string $reason = null): User
    {
        return $this->changeTo($user, AccountStatus::Suspended, $actor, $reason);
    }

    /**
     * Return a suspended account to normal access.
     *
     * An account that never set a password returns to the invited state.
     */
    public function reinstate(User $user, ?User $actor = null, ?string $reason = null): User
    {
        $target = $user->password === null
            ? AccountStatus::Invited
            : AccountStatus::Active;

        return $this->changeTo($user, $target, $actor, $reason);
    }

    /**
     * Close an account and keep it readable for history.
     */
    public function archive(User $user, ?User $actor = null, ?string $reason = null): User
    {
        return $this->changeTo($user, AccountStatus::Archived, $actor, $reason);
    }

    /**
     * Apply the new state and raise the audit event.
     */
    public function changeTo(User $user, AccountStatus $status, ?User $actor = null, ?string $reason = null): User
    {
        if ($this->systemPermissionScope->allows($user, PlatformPermission::ManagePlatform) && !$status->canAccessApplication()) {
            throw new RuntimeException('A platform administrator account cannot be suspended or archived.');
        }

        $from = $user->account_status;

        if ($from === $status) {
            return $user;
        }

        $user->account_status = $status;
        $user->save();

        if (!$status->canAccessApplication()) {
            $this->revokeAccountInvitation->revoke($user);
        }

        AccountStatusChanged::dispatch($user, $from, $status, $actor, $reason);

        return $user;
    }
}
