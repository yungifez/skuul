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
     * @param  mixed  $user
     * @return User
     */
    public function update($user, array $input)
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:100', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:3000'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
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
                'nationality' => $validated['nationality'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? '',
            ])->save();
        }

        return $user;
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @return User
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'birthday' => $input['birthday'],
            'address' => $input['address'] ?? null,
            'nationality' => $input['nationality'] ?? null,
            'state' => $input['state'] ?? null,
            'city' => $input['city'] ?? null,
            'gender' => $input['gender'] ?? null,
            'phone' => $input['phone'] ?? '',
        ])->save();

        $user->sendEmailVerificationNotification();

        return $user;
    }
}
