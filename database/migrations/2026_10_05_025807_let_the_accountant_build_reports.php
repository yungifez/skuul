<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Let the accountant build the reports it can already read.
 *
 * The accountant role got `read report` without `create report`, so its
 * report desk was empty and it could not reconcile. Only an accountant role
 * that still reads reports gets the grant, so a role an admin narrowed stays
 * as it is.
 */
return new class extends Migration
{
    public function up(): void
    {
        $createReport = DB::table('permissions')->where('name', 'create report')->where('guard_name', 'web')->value('id');
        $readReport = DB::table('permissions')->where('name', 'read report')->where('guard_name', 'web')->value('id');

        if ($createReport === null || $readReport === null) {
            return;
        }

        $roleIds = DB::table('roles')
            ->where('name', 'accountant')
            ->whereExists(fn ($query) => $query
                ->select(DB::raw(1))
                ->from('role_has_permissions')
                ->whereColumn('role_has_permissions.role_id', 'roles.id')
                ->where('role_has_permissions.permission_id', $readReport))
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $createReport,
                'role_id' => $roleId,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Keep the grant. A fresh install gets the same one from the seeder.
     */
    public function down(): void {}
};
