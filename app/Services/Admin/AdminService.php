<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\User\UserService;

class AdminService  
{
    public $user;

    public function __construct( UserService $user)
    {
        $this->user = $user;
    }

    //gets active admins
    public function getAllAdmins()
    {
        return $this->user->getUsersByRole('admin');
    }

    public function createAdmin($record)
    {
        $admin = $this->user->createUser($record);

        $admin->assignRole('admin');

        return session()->flash('success', 'Admin Created Successfully');
    }

    //update admin method

    public function updateAdmin(User $admin, $records)
    {
        $this->user->updateUser($admin, $records, 'admin');

        return session()->flash('success', 'Admin Updated Successfully');
    }

    //delete admin method

    public function deleteTeacher(User $admin)
    {
        $this->user->deleteUser($admin);

        return session()->flash('success', 'Admin Deleted Successfully');
    }
}
