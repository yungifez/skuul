<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Let school administrators approve syllabi and see coverage across the school.
     */
    public function up(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'approve syllabus']);

        foreach (DB::table('roles')->where('name', 'admin')->pluck('id') as $roleId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permission->id,
                'role_id' => $roleId,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::query()->where('name', 'approve syllabus')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
