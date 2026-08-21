<?php

namespace App\Actions\Identity;

use App\Models\User;

/**
 * Stop every unused invitation link for an account.
 */
class RevokeAccountInvitation
{
    /**
     * Revoke the pending invitations and return how many were revoked.
     */
    public function revoke(User $user): int
    {
        return $user->accountInvitations()
            ->pending()
            ->update(['revoked_at' => now()]);
    }
}
