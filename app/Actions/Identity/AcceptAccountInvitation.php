<?php

namespace App\Actions\Identity;

use App\Actions\Fortify\PasswordValidationRules;
use App\Enums\AccountStatus;
use App\Events\AccountStatusChanged;
use App\Models\AccountInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Turn an invited account into an active account.
 *
 * The person supplies the plain token and a new password. The action verifies
 * the token, stores the password, and activates the account.
 */
class AcceptAccountInvitation
{
    use PasswordValidationRules;

    /**
     * Find the invitation a plain token points to, or return null.
     */
    public function findPendingInvitation(string $token): ?AccountInvitation
    {
        return AccountInvitation::query()
            ->with('user')
            ->pending()
            ->where('token_hash', AccountInvitation::hashToken($token))
            ->first();
    }

    /**
     * Accept the invitation and activate the account.
     *
     * @param array<string, mixed> $input
     *
     * @throws ValidationException when the token or the password is not valid
     */
    public function accept(string $token, array $input): User
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $tokenHash = AccountInvitation::hashToken($token);

        return DB::transaction(function () use ($tokenHash, $input): User {
            // Lock the invitation before checking it so two simultaneous
            // requests cannot both consume the same one-time token.
            $invitation = AccountInvitation::query()
                ->pending()
                ->where('token_hash', $tokenHash)
                ->lockForUpdate()
                ->first();

            if ($invitation === null) {
                throw ValidationException::withMessages([
                    'token' => 'This invitation link is not valid, or it expired. Ask your administrator to send a new one.',
                ]);
            }

            $invitation->load('user');
            $user = $invitation->user;
            $previousStatus = $user->account_status;

            $user->forceFill([
                'password'          => Hash::make($input['password']),
                'account_status'    => AccountStatus::Active,
                'email_verified_at' => $user->email_verified_at ?? now(),
                'remember_token'    => null,
            ])->save();

            $invitation->forceFill(['accepted_at' => now()])->save();

            $user->accountInvitations()
                ->pending()
                ->update(['revoked_at' => now()]);

            AccountStatusChanged::dispatch($user, $previousStatus, AccountStatus::Active);

            return $user;
        });
    }
}
