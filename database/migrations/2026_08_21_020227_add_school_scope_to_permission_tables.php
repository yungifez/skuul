<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * Roles and permissions become school-scoped. This is the Spatie "teams"
     * feature with `school_id` as the team key.
     *
     * A role definition with a null `school_id` is a shared template that every
     * school can use. Each assignment in `model_has_roles` names the one school
     * it applies to, so a person can hold different roles in different schools.
     *
     * A fresh database already has these columns, because the Spatie migration
     * reads `permission.teams` when it runs. This migration only upgrades a
     * database that was created before the setting was turned on.
     */
    public function up(): void
    {
        if (Schema::hasColumn('model_has_roles', 'school_id')) {
            return;
        }

        Schema::table('roles', function (Blueprint $table): void {
            $table->unsignedBigInteger('school_id')->nullable()->after('id');
            $table->dropUnique(['name', 'guard_name']);
            $table->unique(['school_id', 'name', 'guard_name']);
            $table->index('school_id', 'roles_school_id_index');
        });

        Schema::table('model_has_roles', function (Blueprint $table): void {
            $table->dropPrimary('model_has_roles_role_model_type_primary');
            $table->unsignedBigInteger('school_id')->nullable()->after('model_id');
            $table->index('school_id', 'model_has_roles_school_id_index');
        });

        Schema::table('model_has_permissions', function (Blueprint $table): void {
            $table->dropPrimary('model_has_permissions_permission_model_type_primary');
            $table->unsignedBigInteger('school_id')->nullable()->after('model_id');
            $table->index('school_id', 'model_has_permissions_school_id_index');
        });

        $this->scopeExistingAssignmentsToTheirSchool();

        Schema::table('model_has_roles', function (Blueprint $table): void {
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
            $table->primary(
                ['school_id', 'role_id', 'model_id', 'model_type'],
                'model_has_roles_role_model_type_primary'
            );
        });

        Schema::table('model_has_permissions', function (Blueprint $table): void {
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
            $table->primary(
                ['school_id', 'permission_id', 'model_id', 'model_type'],
                'model_has_permissions_permission_model_type_primary'
            );
        });
    }

    /**
     * Point every existing assignment at the school the person belonged to.
     *
     * A person without a school keeps their assignment against school 0, which
     * no school uses. The assignment stays readable but grants nothing.
     */
    private function scopeExistingAssignmentsToTheirSchool(): void
    {
        if (!Schema::hasColumn('users', 'school_id')) {
            return;
        }

        foreach (['model_has_roles', 'model_has_permissions'] as $table) {
            DB::table($table)
                ->where('model_type', 'App\\Models\\User')
                ->update([
                    'school_id' => DB::raw('coalesce((select school_id from users where users.id = '.$table.'.model_id), 0)'),
                ]);

            DB::table($table)->whereNull('school_id')->update(['school_id' => 0]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Turning teams off again is the job of the Spatie migration, so this only
     * needs to leave the tables in place.
     */
    public function down(): void
    {
        //
    }
};
