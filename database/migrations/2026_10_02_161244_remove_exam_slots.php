<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Remove exam slots. The gradebook replaced them, nothing linked to them,
     * and no gradebook column was ever tied to one.
     */
    public function up(): void
    {
        Schema::table('grade_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('exam_slot_id');
        });

        Schema::dropIfExists('exam_slots');

        $permissions = [
            'create exam slot',
            'read exam slot',
            'update exam slot',
            'delete exam slot',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->select('id');

        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', $permissions)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        throw new LogicException('Exam slots cannot be restored.');
    }
};
