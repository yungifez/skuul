<?php

namespace App\Actions\Identity;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Fortify\PasswordValidationRules;
use App\Enums\AccountStatus;
use App\Enums\AuditAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Set a person's password from a trusted account manager screen.
 */
class SetAccountPassword
{
    use PasswordValidationRules;

    public function __construct(
        private RevokeAccountInvitation $revokeAccountInvitation,
        private RecordAuditEvent $recordAuditEvent,
    ) {}

    /**
     * Set the password and optionally require the person to choose another one.
     */
    public function set(User $user, string $password, bool $forceReset, ?User $actor = null): User
    {
        return DB::transaction(function () use ($user, $password, $forceReset, $actor): User {
            $user->forceFill([
                'password' => Hash::make($password),
                'password_change_required_at' => $forceReset ? now() : null,
                'account_status' => AccountStatus::Active,
                'email_verified_at' => $user->email_verified_at ?? now(),
                'remember_token' => null,
            ])->save();

            $this->revokeAccountInvitation->revoke($user, $actor);
            $this->recordAuditEvent->record(
                AuditAction::AccountPasswordChanged,
                $user,
                [
                    'email' => $user->email,
                    'force_reset' => $forceReset,
                ],
                $actor,
            );

            return $user;
        });
    }
}
