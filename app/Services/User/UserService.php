<?php

namespace App\Services\User;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Identity\ChangeAccountStatus;
use App\Actions\Identity\ProvisionAccount;
use App\Actions\Identity\SendAccountInvitation;
use App\Actions\School\EndSchoolMembership;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        public ProvisionAccount $provisionAccountAction,
        public SendAccountInvitation $sendAccountInvitationAction,
        public ChangeAccountStatus $changeAccountStatusAction,
        public UpdateUserProfileInformation $updateUserProfileInformationAction,
        public EndSchoolMembership $endSchoolMembershipAction,
    ) {}

    /**
     * Get all users.
     */
    public function getAllUsers(): Collection|static
    {
        return User::ofSchool()->get();
    }

    /**
     * Get a user by id.
     *
     * @param  int|array<int, int>  $id
     * @return User|Collection<int, User>|null
     */
    public function getUserById($id)
    {
        return User::find($id);
    }

    /**
     * Get users by role.
     *
     * @param  string  $role
     * @return Collection<int, User>
     */
    public function getUsersByRole($role)
    {
        return User::role($role)->ofSchool()->get();
    }

    /**
     * Provision an account for a new member of the school.
     *
     * The account has no password. The person receives a one-time invitation
     * and sets their own password. Calling this again with the same email
     * updates the existing profile instead of creating a second login.
     *
     * @param  array|\Illuminate\Support\Collection  $record
     */
    public function createUser($record, bool $invite = true): User
    {
        $record['school_id'] = $record['school_id'] ?? current_school_id();

        $user = $this->provisionAccountAction->provision([
            'name' => $record['name'],
            'email' => $record['email'],
            'photo' => $record['profile_photo'] ?? null,
            'school_id' => $record['school_id'],
            'birthday' => $record['birthday'] ?? null,
            'address' => $record['address'] ?? null,
            'address_line_2' => $record['address_line_2'] ?? null,
            'country' => $record['country'] ?? null,
            'nationality' => $record['nationality'] ?? null,
            'state' => $record['state'] ?? null,
            'city' => $record['city'] ?? null,
            'postal_code' => $record['postal_code'] ?? null,
            'gender' => $record['gender'] ?? null,
            'phone' => $record['phone'] ?? null,
        ]);

        if ($invite && $user->isAwaitingInvitationAcceptance()) {
            $this->sendAccountInvitationAction->send($user, auth()->user());
        }

        return $user;
    }

    /**
     * Check if user has a role.
     *
     * @param  int  $id
     * @param  string  $role
     * @return bool
     */
    public function verifyRole($id, $role)
    {
        $user = $this->getUserById($id);

        return $user->load('roles')->hasRole($role);
    }

    /**
     * Update user profile information.
     *
     * @param  User  $user  User instance
     * @param  string  $role  Verify role before updating
     * @return User
     */
    public function updateUser(User $user, $record, ?string $role = null)
    {
        if (isset($role)) {
            if (!$this->verifyRole($user->id, $role)) {
                abort('403', "User isn't a/an $role");
            }
        }
        // A person who also belongs to another school, or holds authority
        // beyond this one, signs in there with this email. A new email is a
        // new way to reset their password, so only they may change it.
        if (isset($record['email']) && mb_strtolower((string) $record['email']) !== mb_strtolower((string) $user->email)) {
            if ($user->belongsToAnotherSchool()) {
                throw ValidationException::withMessages([
                    'email' => 'This person also belongs to another school, so only they can change their email.',
                ]);
            }

            if ($user->holdsPowerBeyond(current_school_id())) {
                throw ValidationException::withMessages([
                    'email' => 'This person has authority beyond this school, so only they can change their email.',
                ]);
            }
        }

        $user = $this->updateUserProfileInformationAction->update($user, [
            'name' => $record['name'],
            'email' => $record['email'],
            'photo' => $record['profile_photo'] ?? null,
            'birthday' => $record['birthday'] ?? null,
            'address' => $record['address'] ?? null,
            'address_line_2' => $record['address_line_2'] ?? null,
            'country' => $record['country'] ?? null,
            'nationality' => $record['nationality'] ?? null,
            'state' => $record['state'] ?? null,
            'city' => $record['city'] ?? null,
            'postal_code' => $record['postal_code'] ?? null,
            'gender' => $record['gender'] ?? null,
            'phone' => $record['phone'] ?? null,
        ]);

        return $user;
    }

    /**
     * Refuse to add a person to this school in a role they already hold here.
     *
     * @throws ValidationException
     */
    public function failIfAlreadyHolds(string $email, Role $role): void
    {
        $person = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])->first();

        if ($person !== null && $person->belongsToSchool(current_school_id()) && $person->hasRole($role->value)) {
            throw ValidationException::withMessages([
                'email' => "{$person->name} already holds the ".strtolower($role->label()).' role at this school.',
            ]);
        }
    }

    /**
     * Delete a user, or only remove them from the working school.
     *
     * One account serves every school the person belongs to. When another
     * school still has them, only this school's access ends, so that school
     * keeps its teacher, parent or learner.
     */
    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user): void {
            // Ending the membership first ends what the person still does
            // here, so a deleted teacher leaves no lesson or cover behind.
            $this->endSchoolMembershipAction->end($user, current_school());

            if (!$user->keepsAccountWhenRemovedHere()) {
                $user->delete();
            }
        });
    }

    /**
     * verify user role or return 404.
     */
    public function verifyUserIsOfRoleElseNotFound(User $user, string $role)
    {
        if (!$this->verifyRole($user->id, $role)) {
            abort(404);
        }
    }

    /**
     * Suspend a user account without deleting the person profile.
     */
    public function suspendUserAccount(User $user, ?string $reason = null): User
    {
        return $this->changeAccountStatusAction->suspend($user, auth()->user(), $reason);
    }

    /**
     * Return a suspended account to normal access.
     */
    public function reinstateUserAccount(User $user, ?string $reason = null): User
    {
        return $this->changeAccountStatusAction->reinstate($user, auth()->user(), $reason);
    }
}
