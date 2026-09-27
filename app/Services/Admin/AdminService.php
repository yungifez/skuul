<?php

namespace App\Services\Admin;

use App\Enums\Role;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function getAllAdmins()
    {
        return $this->user->getUsersByRole('admin');
    }

    /**
     * Create Admin.
     *
     * @param  array<string, mixed>  $records
     */
    public function createAdmin(array $records): User
    {
        $this->user->failIfAlreadyHolds($records['email'], Role::Admin);

        return DB::transaction(function () use ($records): User {
            $admin = $this->user->createUser($records);
            $admin->assignRole(Role::Admin);

            return $admin;
        });
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
