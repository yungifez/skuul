<?php

namespace App\Services\User;

use App\Models\User;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;

class UserService
{
    public $createUserAction;
    public $updateUserProfileInformationAction;
    //construct method which assigns CresteNewUser to createUserAction variable

    public function __construct(CreateNewUser $createUserAction, UpdateUserProfileInformation $updateUserProfileInformationAction)
    {
        $this->createUserAction = $createUserAction;
        $this->updateUserProfileInformationAction = $updateUserProfileInformationAction;
    }

    public function getAllUsers()
    {
        return User::where('school_id', auth()->user()->school_id)->get();
    }

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function getUsersByRole($role)
    {
        return User::Role($role)->where('school_id', auth()->user()->school_id)->get();
    }

    public function createUser($record)
    {
        $record['name'] = $this->createFullName($record['first_name'],$record['last_name']);
        $record['school_id'] = auth()->user()->school_id;
        $user =  $this->createUserAction->create([
            'name' => $record['name'],
            'email' => $record['email'],
            'password' => $record['password'],
            'school_id' => $record['school_id'],
            'birthday' => $record['birthday'],
            'password_confirmation' => $record['password_confirmation'],
            'address' => $record['address'],
            'blood_group' => $record['blood_group'],
            'religion' => $record['religion'],
            'nationality' => $record['nationality'],
            'state' => $record['state'],
            'city' => $record['city'],
            'gender' => $record['gender'],
            'phone' => $record['phone']
        ]);
    
        if (isset($record['profile_photo'])) {
            $user->updateProfilePhoto($record['profile_photo']);
        }

        return $user;
    }

    // name concatenation

    public function createFullName($firstname, $lastname, $othernames = null)
    {
        return $firstname . ' ' . $lastname. ' ' . $othernames;
    }

    // verify role

    public function verifyRole($id, $role)
    {
        $user = $this->getUserById($id);

        return $user->hasRole($role);
    }
}