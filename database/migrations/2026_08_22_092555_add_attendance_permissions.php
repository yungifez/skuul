<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = ['read attendance', 'take attendance', 'menu-attendance'];
        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore(['name' => $permission, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]);
        }
        $ids = DB::table('permissions')->whereIn('name', $permissions)->pluck('id', 'name');
        foreach (DB::table('roles')->whereIn('name', ['admin', 'platform-admin', 'teacher'])->pluck('id') as $roleId) {
            foreach ($ids as $id) {
                DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $id, 'role_id' => $roleId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Attendance permissions are part of the current authorization model.');
    }
};
