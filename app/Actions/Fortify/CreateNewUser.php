<?php

namespace App\Actions\Fortify;

use Throwable;
use App\Models\User;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array $input
     *
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            // 'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'school_id'   => ['required', 'exists:schools,id'],
            'birthday'    => ['required', 'date', 'date_format:Y/m/d', 'before:today'],
            'address'     => ['required', 'string', 'max:500'],
            'blood_group' => ['required', 'string', 'max:255'],
            'religion'    => ['nullable', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:255'],
            'state'       => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:255'],
            'gender'      => ['required', 'string', 'max:255'],
            'phone'       => ['string', 'max:255'],
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

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            report("Could not send email to $user->email. $e");

            return $user;
        }
        
        return $user;
    }
}
