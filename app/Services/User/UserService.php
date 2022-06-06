<?php

namespace App\Services\User;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;

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

    /**
     * Get a user by id
     *
     * @param integer $id
     * @return App\Models\User
     */
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
        if (!$record['other_names']) {
            $record['other_names'] = null;
        }
        $record['name'] = $this->createFullName($record['first_name'], $record['last_name'], $record['other_names']);
        $record['school_id'] = auth()->user()->school_id;
        $user = $this->createUserAction->create([
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
            'phone' => $record['phone'],
        ]);

        if (isset($record['profile_photo'])) {
            $user->updateProfilePhoto($record['profile_photo']);
        }

        return $user;
    }

    // name concatenation

    public function createFullName($firstname, $lastname, $othernames = null)
    {
        return $firstname.' '.$lastname.' '.$othernames;
    }

    // verify role

    public function verifyRole($id, $role)
    {
        $user = $this->getUserById($id);

        return $user->hasRole($role);
    }

    public function updateUser(User $user, $record, $role = null)
    {
        if (isset($role)) {
            if (!$this->verifyRole($user->id,$role)) {
                abort('403', "User isn't a/an $role");
            }
        }
       
        if (!$record['other_names']) {
            $record['other_names'] = null;
        }
        
        $record['name'] = $this->createFullName($record['first_name'], $record['last_name'], $record['other_names']);

        $user = $this->updateUserProfileInformationAction->update($user, $record);

        return $user;
    }

    public function verifyUserIsOfRoleElseNotFound(User $user, $role)
    {
        if (!$this->verifyRole($user->id,$role)) {
            abort(404);
        }
    }
}
