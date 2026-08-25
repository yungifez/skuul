<?php

namespace App\Services\User;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Identity\ChangeAccountStatus;
use App\Actions\Identity\ProvisionAccount;
use App\Actions\Identity\SendAccountInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        public ProvisionAccount $provisionAccountAction,
        public SendAccountInvitation $sendAccountInvitationAction,
        public ChangeAccountStatus $changeAccountStatusAction,
        public UpdateUserProfileInformation $updateUserProfileInformationAction,
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
            'country' => $record['country'] ?? null,
            'state' => $record['state'] ?? null,
            'city' => $record['city'] ?? null,
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
        // update profile photo if present
        if (isset($record['profile_photo'])) {
            $user->updateProfilePhoto($record['profile_photo']);
        }

        $user = $this->updateUserProfileInformationAction->update($user, [
            'name' => $record['name'],
            'email' => $record['email'],
            'birthday' => $record['birthday'] ?? null,
            'address' => $record['address'] ?? null,
            'country' => $record['country'] ?? null,
            'state' => $record['state'] ?? null,
            'city' => $record['city'] ?? null,
            'gender' => $record['gender'] ?? null,
            'phone' => $record['phone'] ?? null,
        ]);

        return $user;
    }

    /**
     * Delete a user.
     *
     * @param  string  $role
     * @return void
     */
    public function deleteUser(User $user)
    {
        $user->delete();
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
