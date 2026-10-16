<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Give existing installs the permissions that were only added to the seeder.
 *
 * The seeder runs once, at install. Every permission added to it later never
 * reached a running school, so finance, calendar, boarding, library and role
 * screens were closed to everyone there. Each missing permission is created
 * and granted to the built-in roles the seeder grants it to. A permission the
 * install already has is left alone, so a school's own changes are kept.
 */
return new class extends Migration
{
    /**
     * The roles that receive each permission, as the seeder lists them.
     *
     * @var array<string, list<string>>
     */
    private array $grants = [
        'approve campus move' => ['admin'],
        'approve result' => ['admin'],
        'book facility' => ['admin'],
        'create calendar event' => ['admin'],
        'create cash deposit' => ['admin'],
        'create expense' => ['admin'],
        'decide overnight leave' => ['admin'],
        'delete calendar event' => ['admin'],
        'lend library item' => ['admin'],
        'manage admission waitlist' => ['admin'],
        'manage boarding' => ['admin'],
        'manage budget' => ['admin'],
        'manage facility' => ['admin'],
        'manage financial period' => ['admin'],
        'manage grading scale' => ['admin'],
        'manage library' => ['admin'],
        'manage role' => ['admin'],
        'migrate instructional model' => ['admin'],
        'publish calendar event' => ['admin'],
        'read admission waitlist' => ['admin'],
        'read boarding' => ['admin'],
        'read budget' => ['admin'],
        'read calendar event' => ['admin', 'teacher', 'student', 'parent'],
        'read cash deposit' => ['admin'],
        'read expense' => ['admin'],
        'read facility' => ['admin'],
        'read financial period' => ['admin'],
        'read library' => ['admin'],
        'read ranking' => ['admin', 'teacher'],
        'read role' => ['admin'],
        'refund student payment' => ['admin'],
        'request campus move' => ['admin'],
        'update calendar event' => ['admin'],
    ];

    public function up(): void
    {
        foreach ($this->grants as $name => $roleNames) {
            if (Permission::query()->where('name', $name)->where('guard_name', 'web')->exists()) {
                continue;
            }

            $permission = Permission::findOrCreate($name, 'web');

            $roleIds = DB::table('roles')
                ->whereIn('name', [...$roleNames, 'platform-admin'])
                ->pluck('id');

            foreach ($roleIds as $roleId) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permission->id,
                    'role_id' => $roleId,
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Keep the permissions. A fresh install gets the same ones from the
     * seeder, so taking them away would close those screens there too.
     */
    public function down(): void {}
};
