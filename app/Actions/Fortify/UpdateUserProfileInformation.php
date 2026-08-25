<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param mixed $user
     *
     * @return User
     */
    public function update($user, array $input)
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:100', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:3000'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:100'],
        ])->validate();

        if (isset($validated['photo'])) {
            $user->updateProfilePhoto($validated['photo']);
        }

        if ($validated['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $validated);
        } else {
            $user->forceFill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'birthday' => $validated['birthday'],
                'address' => $validated['address'] ?? null,
                'address_line_2' => array_key_exists('address_line_2', $validated)
                    ? $validated['address_line_2']
                    : $user->address_line_2,
                'country' => array_key_exists('country', $validated)
                    ? $validated['country']
                    : $user->country,
                'nationality' => $validated['nationality'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'postal_code' => array_key_exists('postal_code', $validated)
                    ? $validated['postal_code']
                    : $user->postal_code,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? '',
            ])->save();
        }

        return $user;
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param mixed $user
     *
     * @return User
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name'              => $input['name'],
            'email'             => $input['email'],
            'email_verified_at' => null,
            'birthday' => $input['birthday'],
            'address' => $input['address'] ?? null,
            'address_line_2' => array_key_exists('address_line_2', $input)
                ? $input['address_line_2']
                : $user->address_line_2,
            'country' => array_key_exists('country', $input)
                ? $input['country']
                : $user->country,
            'nationality' => $input['nationality'] ?? null,
            'state' => $input['state'] ?? null,
            'city' => $input['city'] ?? null,
            'postal_code' => array_key_exists('postal_code', $input)
                ? $input['postal_code']
                : $user->postal_code,
            'gender' => $input['gender'] ?? null,
            'phone' => $input['phone'] ?? '',
        ])->save();

        $user->sendEmailVerificationNotification();

        return $user;
    }
}
