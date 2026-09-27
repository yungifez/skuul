<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * An install made before a permission existed still gets it on upgrade.
 */
class PermissionBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_old_install_gets_the_missing_permissions_on_the_right_roles(): void
    {
        Permission::query()->whereIn('name', ['create cash deposit', 'read calendar event', 'read ranking'])->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->runBackfill();

        $this->assertTrue($this->role('admin')->hasPermissionTo('create cash deposit'));
        $this->assertTrue($this->role('platform-admin')->hasPermissionTo('create cash deposit'));
        $this->assertFalse($this->role('teacher')->hasPermissionTo('create cash deposit'));

        foreach (['admin', 'teacher', 'student', 'parent'] as $roleName) {
            $this->assertTrue($this->role($roleName)->hasPermissionTo('read calendar event'), "{$roleName} cannot read the calendar.");
        }

        $this->assertTrue($this->role('teacher')->hasPermissionTo('read ranking'));
        $this->assertFalse($this->role('parent')->hasPermissionTo('read ranking'));
    }

    public function test_the_backfill_keeps_a_schools_own_changes_to_a_role(): void
    {
        $this->role('teacher')->givePermissionTo('read budget');
        $this->role('admin')->revokePermissionTo('read budget');

        $this->runBackfill();

        $this->assertTrue($this->role('teacher')->hasPermissionTo('read budget'));
        $this->assertFalse($this->role('admin')->hasPermissionTo('read budget'), 'A permission the install already had must not be granted again.');
    }

    public function test_the_backfill_can_run_twice(): void
    {
        $this->runBackfill();
        $grants = DB::table('role_has_permissions')->count();

        $this->runBackfill();

        $this->assertSame($grants, DB::table('role_has_permissions')->count());
    }

    private function runBackfill(): void
    {
        $migration = require database_path('migrations/2026_09_27_074218_backfill_permissions_added_after_install.php');
        $migration->up();
    }

    private function role(string $name): Role
    {
        return Role::query()->whereNull('school_id')->where('name', $name)->firstOrFail()->refresh();
    }
}
