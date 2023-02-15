<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\User\UserService;

class AdminService
{
    /**
     * @var UserService
     */
    public $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Get all Admins.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllAdmins()
    {
        return $this->user->getUsersByRole('admin');
    }

    /**
     * Create Admin.
     *
     * @param array|Collection $records
     *
     * @return \App\Models\User
     * @return void
     */
    public function createAdmin($records)
    {
        $admin = $this->user->createUser($records);
        $admin->assignRole('admin');
    }

    /**
     * Update Admin.
     *
     * @param array|Collection $records
     *
     * @return void
     */
    public function updateAdmin(User $admin, $records)
    {
        $this->user->updateUser($admin, $records, 'admin');
    }

    /**
     * Delete Admin.
     *
     *
     * @return void
     */
    public function deleteAdmin(User $admin)
    {
        $this->user->deleteUser($admin);
    }
}
