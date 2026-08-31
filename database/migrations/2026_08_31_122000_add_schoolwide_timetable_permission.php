<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'create schoolwide timetable']);

        foreach (DB::table('roles')->where('name', 'admin')->pluck('id') as $roleId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permission->id,
                'role_id' => $roleId,
            ]);
        }
    }

    public function down(): void
    {
        Permission::query()->where('name', 'create schoolwide timetable')->delete();
    }
};
