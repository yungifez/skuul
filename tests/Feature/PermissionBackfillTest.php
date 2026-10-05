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

    public function test_a_new_install_gives_the_librarian_and_accountant_their_work(): void
    {
        $this->assertTrue($this->role('librarian')->hasPermissionTo('lend library item'));
        $this->assertFalse($this->role('librarian')->hasPermissionTo('create fee invoice'));
        $this->assertTrue($this->role('accountant')->hasPermissionTo('create fee invoice'));
        $this->assertTrue($this->role('accountant')->hasPermissionTo('create cash deposit'));
        $this->assertFalse($this->role('accountant')->hasPermissionTo('delete fee invoice'));
        $this->assertFalse($this->role('accountant')->hasPermissionTo('manage financial period'));
        $this->assertTrue($this->role('accountant')->hasPermissionTo('create report'));
    }

    public function test_an_old_install_fills_only_an_empty_librarian_or_accountant(): void
    {
        $this->role('librarian')->syncPermissions([]);
        $this->role('accountant')->syncPermissions(['read fee invoice']);

        $migration = require database_path('migrations/2026_09_27_075808_grant_librarian_and_accountant_their_work.php');
        $migration->up();

        $this->assertTrue($this->role('librarian')->hasPermissionTo('manage library'));
        $this->assertSame(['read fee invoice'], $this->role('accountant')->permissions->pluck('name')->all());
    }

    public function test_an_old_install_lets_the_accountant_build_reports(): void
    {
        $this->role('accountant')->revokePermissionTo('create report');

        $this->runAccountantReportGrant();
        $this->runAccountantReportGrant();

        $this->assertTrue($this->role('accountant')->hasPermissionTo('create report'));
    }

    public function test_the_report_grant_keeps_an_accountant_an_admin_narrowed(): void
    {
        $this->role('accountant')->syncPermissions(['read fee invoice']);

        $this->runAccountantReportGrant();

        $this->assertSame(['read fee invoice'], $this->role('accountant')->permissions->pluck('name')->all());
    }

    private function runAccountantReportGrant(): void
    {
        $migration = require database_path('migrations/2026_10_05_025807_let_the_accountant_build_reports.php');
        $migration->up();
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
