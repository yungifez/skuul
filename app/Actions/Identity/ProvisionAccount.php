<?php

namespace App\Actions\Identity;

use App\Actions\School\GrantSchoolMembership;
use App\Enums\AccountStatus;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * Create the person profile and a pending account for a new member of a school.
 *
 * The account has no password. The person sets one from an invitation link.
 * Calling this action again with the same email returns the existing account
 * and adds the school membership, so provisioning is safe to retry and one
 * person can join a second school without a second login.
 */
class ProvisionAccount
{
    public function __construct(private GrantSchoolMembership $grantSchoolMembership) {}

    /**
     * Provision an account and return the user.
     *
     * @param  array<string, mixed>  $input
     */
    public function provision(array $input): User
    {
        $data = Validator::make($input, [
            'name' => ['required', 'string', 'max:511'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:511'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:3000'],
            'school_id' => ['required', 'exists:schools,id'],
            'birthday' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string', 'max:500'],
            'blood_group' => ['required', 'string', 'max:255'],
            'religion' => ['nullable', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $user = User::where('email', $data['email'])->first();

        if ($user === null) {
            $user = new User;
            $user->password = null;
            $user->account_status = AccountStatus::Invited;
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'birthday' => $data['birthday'],
            'address' => $data['address'],
            'blood_group' => $data['blood_group'],
            'religion' => $data['religion'] ?? null,
            'nationality' => $data['nationality'],
            'state' => $data['state'],
            'city' => $data['city'],
            'gender' => $data['gender'],
            'phone' => $data['phone'] ?? null,
        ]);

        $user->save();

        // School access is a membership record, never a column on the user.
        $this->grantSchoolMembership->grant($user, School::findOrFail($data['school_id']));

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        return $user;
    }
}
