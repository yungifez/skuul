<?php

namespace App\Actions\Identity;

use App\Actions\School\GrantSchoolMembership;
use App\Enums\AccountStatus;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Create the person profile and a pending account for a new member of a school.
 *
 * The account has no password. The person sets one from an invitation link.
 * Calling this action again with the same email returns the existing account
 * and adds the school membership, so provisioning is safe to retry and one
 * person can join a second campus of the organization without a second login.
 * The second call never overwrites the profile the person already has; it only
 * fills blanks. An account the organization has never held is refused.
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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', new_email_rule(), 'max:100'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:3000'],
            'school_id' => ['required', 'exists:schools,id'],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', Rule::in(['Male', 'Female', 'Non-binary', 'Prefer not to say'])],
            'phone' => ['nullable', 'string', 'max:100'],
        ])->validate();

        $school = School::findOrFail($data['school_id']);
        $user = User::where('email', $data['email'])->first();
        $isExistingPerson = $user !== null;

        // Another organization's person is not this one's to attach. Their
        // profile holds what that organization recorded, and they never
        // agreed to share it here.
        if ($isExistingPerson && !$user->isKnownToOrganization($school->organization_id)) {
            throw ValidationException::withMessages([
                'email' => 'This email belongs to a person outside your organization, so it cannot be used here.',
            ]);
        }

        if ($user === null) {
            $user = new User;
            $user->password = null;
            $user->account_status = AccountStatus::Invited;
        }

        $profile = [
            'name' => $data['name'],
            'email' => $data['email'],
            'birthday' => $data['birthday'] ?? null,
            'address' => $data['address'] ?? null,
            'address_line_2' => $data['address_line_2'] ?? null,
            'country' => $data['country'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'state' => $data['state'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'gender' => $data['gender'] ?? null,
            'phone' => $data['phone'] ?? null,
        ];

        // A person who already has an account keeps the profile they or
        // another school wrote. Joining a school only fills what is blank.
        if ($isExistingPerson) {
            $profile = array_filter(
                $profile,
                fn (mixed $value, string $attribute): bool => $value !== null && blank($user->getAttribute($attribute)),
                ARRAY_FILTER_USE_BOTH,
            );
        }

        $user->fill($profile);

        $user->save();

        // School access is a membership record, never a column on the user.
        $this->grantSchoolMembership->grant($user, $school);

        if (isset($data['photo']) && !($isExistingPerson && $user->profile_photo_path !== null)) {
            $user->updateProfilePhoto($data['photo']);
        }

        return $user;
    }
}
