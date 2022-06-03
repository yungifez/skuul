<?php

namespace App\Services\Admin;

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
}
