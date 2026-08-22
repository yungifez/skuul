<?php

namespace App\Actions\Identity;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AccountStatus;
use App\Enums\AuditAction;
use App\Models\AccountInvitation;
use App\Models\User;
use App\Notifications\AccountInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Issue a one-time invitation link and email it to the person.
 *
 * Every new invitation revokes the invitations that came before it, so only
 * the newest link works. The action is safe to call again.
 */
class SendAccountInvitation
{
    public function __construct(private RecordAuditEvent $recordAuditEvent)
    {
    }

    /**
     * Send an invitation to the given account.
     *
     * @param User      $user      the account to invite
     * @param User|null $invitedBy the administrator who sent the invitation
     */
    public function send(User $user, ?User $invitedBy = null): AccountInvitation
    {
        if ($user->account_status === AccountStatus::Archived) {
            throw new RuntimeException('An archived account cannot be invited. Reinstate the account first.');
        }

        $token = Str::random(64);

        $invitation = DB::transaction(function () use ($user, $invitedBy, $token): AccountInvitation {
            $user->accountInvitations()
                ->pending()
                ->update(['revoked_at' => now()]);

            $invitation = $user->accountInvitations()->create([
                'invited_by' => $invitedBy?->id,
                'token_hash' => AccountInvitation::hashToken($token),
                'expires_at' => now()->addHours((int) config('identity.invitations.expires_after_hours')),
            ]);

            if ($user->password === null) {
                $user->account_status = AccountStatus::Invited;
                $user->save();
            }

            $this->recordAuditEvent->record(
                AuditAction::AccountInvitationSent,
                $invitation,
                ['user_id' => $user->id, 'email' => $user->email, 'expires_at' => $invitation->expires_at->toIso8601String()],
                $invitedBy,
            );

            return $invitation;
        });

        $user->notify(new AccountInvitationNotification($token, $invitation->expires_at));

        return $invitation;
    }
}
