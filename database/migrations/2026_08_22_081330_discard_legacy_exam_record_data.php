<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    public function up(): void
    {
        DB::table('exam_records')->delete();
        DB::table('audit_events')
            ->whereIn('action', ['exam.result_published', 'exam.result_unpublished'])
            ->delete();

        $permissions = [
            'create exam record',
            'read exam record',
            'update exam record',
            'delete exam record',
            'check result',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->select('id');

        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }

    public function down(): void
    {
        throw new LogicException('Legacy exam records cannot be restored.');
    }
};
