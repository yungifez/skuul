<?php

namespace App\Actions\Identity;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Stop every unused invitation link for an account.
 */
class RevokeAccountInvitation
{
    public function __construct(private RecordAuditEvent $recordAuditEvent) {}

    /**
     * Revoke the pending invitations and return how many were revoked.
     *
     * @param  User  $user  the account whose links are stopped
     * @param  User|null  $revokedBy  the administrator who stopped them
     */
    public function revoke(User $user, ?User $revokedBy = null): int
    {
        return DB::transaction(function () use ($user, $revokedBy): int {
            $revoked = $user->accountInvitations()
                ->pending()
                ->update(['revoked_at' => now()]);

            if ($revoked > 0) {
                $this->recordAuditEvent->record(
                    AuditAction::AccountInvitationRevoked,
                    $user,
                    ['revoked_count' => $revoked, 'email' => $user->email],
                    $revokedBy,
                );
            }

            return $revoked;
        });
    }
}
