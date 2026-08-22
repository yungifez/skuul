<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    private const SystemTeamId = 0;

    private const GuardName = 'web';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $organizationPermissions = ['read organization', 'manage organization'];
        $platformPermissions = ['access all schools', 'access all organizations', 'manage platform'];

        foreach (array_merge($organizationPermissions, $platformPermissions) as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name'       => $permission,
                'guard_name' => self::GuardName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $platformRoleId = $this->roleId('platform-admin');
        $organizationRoleId = $this->roleId('organization-admin');

        foreach (DB::table('permissions')->pluck('id') as $permissionId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id'       => $platformRoleId,
            ]);
        }

        foreach (DB::table('permissions')->whereIn('name', $organizationPermissions)->pluck('id') as $permissionId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id'       => $organizationRoleId,
            ]);
        }

        $this->assignRoleToUsers(
            $platformRoleId,
            DB::table('users')->where('is_platform_admin', true)->pluck('id')
        );

        $this->assignRoleToUsers(
            $organizationRoleId,
            DB::table('organization_memberships')
                ->where('status', 'active')
                ->where('role', 'admin')
                ->pluck('user_id')
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Access may have changed after this migration. Keep authority records
        // rather than deleting roles or assignments during rollback.
    }

    private function roleId(string $name): int
    {
        $roleId = DB::table('roles')
            ->where('name', $name)
            ->where('guard_name', self::GuardName)
            ->whereNull('school_id')
            ->value('id');

        if ($roleId !== null) {
            return $roleId;
        }

        return DB::table('roles')->insertGetId([
            'name'       => $name,
            'guard_name' => self::GuardName,
            'school_id'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @param Collection<int, int> $userIds
     */
    private function assignRoleToUsers(int $roleId, Collection $userIds): void
    {
        foreach ($userIds->unique() as $userId) {
            DB::table('model_has_roles')->insertOrIgnore([
                'school_id'  => self::SystemTeamId,
                'role_id'    => $roleId,
                'model_type' => 'App\\Models\\User',
                'model_id'   => $userId,
            ]);
        }
    }
};
