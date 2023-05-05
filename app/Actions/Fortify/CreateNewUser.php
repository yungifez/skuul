<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Throwable;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     *
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name'     => ['required', 'string', 'max:511'],
            'email'    => ['required', 'string', 'email:rfc,dns', 'max:511', 'unique:users'],
            'photo'    => ['nullable', 'mimes:jpg,jpeg,png', 'max:3000'],
            'password' => $this->passwordRules(),
            // 'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'school_id'   => ['required', 'exists:schools,id'],
            'birthday'    => ['required', 'date', 'before:today'],
            'address'     => ['required', 'string', 'max:500'],
            'blood_group' => ['required', 'string', 'max:255'],
            'religion'    => ['nullable', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:255'],
            'state'       => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:255'],
            'gender'      => ['required', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:255'],
        ])->validate();

        $user = User::create([
            'name'        => $input['name'],
            'email'       => $input['email'],
            'birthday'    => $input['birthday'],
            'password'    => Hash::make($input['password']),
            'address'     => $input['address'],
            'school_id'   => $input['school_id'],
            'blood_group' => $input['blood_group'],
            'religion'    => $input['religion'],
            'nationality' => $input['nationality'],
            'state'       => $input['state'],
            'city'        => $input['city'],
            'gender'      => $input['gender'],
            'phone'       => $input['phone'],
        ]);

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            report("Could not verification send email to $user->email. $e");

            return $user;
        }

        return $user;
    }
}
