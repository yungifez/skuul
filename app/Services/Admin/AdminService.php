<?php

namespace App\Services\Admin;

use App\Enums\Role;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Support\Collection;

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
     * @param  array<string, mixed>|Collection<string, mixed>  $records
     * @return void
     */
    public function createAdmin($records)
    {
        $admin = $this->user->createUser($records);
        $admin->assignRole(Role::Admin);
    }

    /**
     * Update Admin.
     *
     * @param  array<string, mixed>|Collection<string, mixed>  $records
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
