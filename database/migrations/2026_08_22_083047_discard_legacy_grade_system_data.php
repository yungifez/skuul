<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('grade_systems')->delete();

        $permissions = [
            'create grade system',
            'read grade system',
            'update grade system',
            'delete grade system',
            'menu-grade-system',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->select('id');

        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Legacy grade-system data cannot be restored.');
    }
};
