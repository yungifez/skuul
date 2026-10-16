<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Give the built-in librarian and accountant roles something to do.
 *
 * Both roles were created with no permissions, so a person given either one
 * could open nothing. A role that still holds nothing gets the seeder's
 * defaults. A role a school already filled in is left alone.
 */
return new class extends Migration
{
    /**
     * @var array<string, list<string>>
     */
    private array $defaults = [
        'librarian' => [
            'read student',
            'read library',
            'manage library',
            'lend library item',
        ],
        'accountant' => [
            'read student',
            'read report',
            'create fee',
            'read fee',
            'update fee',
            'create fee category',
            'read fee category',
            'update fee category',
            'create fee invoice',
            'read fee invoice',
            'update fee invoice',
            'create fee invoice record',
            'read fee invoice record',
            'update fee invoice record',
            'refund student payment',
            'read expense',
            'create expense',
            'read cash deposit',
            'create cash deposit',
            'read budget',
            'read financial period',
        ],
    ];

    public function up(): void
    {
        foreach ($this->defaults as $roleName => $permissionNames) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissionNames)
                ->where('guard_name', 'web')
                ->pluck('id');

            $emptyRoleIds = DB::table('roles')
                ->where('name', $roleName)
                ->whereNotExists(fn ($query) => $query
                    ->select(DB::raw(1))
                    ->from('role_has_permissions')
                    ->whereColumn('role_has_permissions.role_id', 'roles.id'))
                ->pluck('id');

            foreach ($emptyRoleIds as $roleId) {
                DB::table('role_has_permissions')->insertOrIgnore($permissionIds->map(fn (int $permissionId): array => [
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ])->all());
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Keep the grants. A fresh install gets the same ones from the seeder.
     */
    public function down(): void {}
};
