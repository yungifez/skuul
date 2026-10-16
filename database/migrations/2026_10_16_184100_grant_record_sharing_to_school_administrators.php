<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Give the built-in school administrator the record sharing permissions.
 *
 * The seeder created these permissions but gave them to no campus role, so
 * only a platform administrator could ask for, approve, or hand over records.
 * A school that already gave one of them to its administrator keeps its choice.
 */
return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $permissions = [
        'request data sharing',
        'approve data sharing',
        'fulfil data sharing',
    ];

    public function up(): void
    {
        $permissionIds = collect($this->permissions)
            ->map(fn (string $name): int => Permission::findOrCreate($name, 'web')->id);

        $adminRoleIds = DB::table('roles')
            ->where('name', 'admin')
            ->whereNotExists(fn ($query) => $query->from('role_has_permissions')
                ->whereColumn('role_has_permissions.role_id', 'roles.id')
                ->whereIn('role_has_permissions.permission_id', $permissionIds))
            ->pluck('id');

        foreach ($adminRoleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Keep the grants. A fresh install gets the same ones from the seeder.
     */
    public function down(): void {}
};
