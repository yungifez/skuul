<?php

namespace Tests\Feature;

use App\Actions\Authorization\GrantSystemRole;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\AccountStatus;
use App\Enums\AuditAction;
use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Models\Installation;
use App\Models\Notice;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use Database\Seeders\RunInProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class InstallationTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = false;

    private static bool $databaseWasRefreshed = false;

    protected function setUp(): void
    {
        if (!self::$databaseWasRefreshed) {
            RefreshDatabaseState::$migrated = false;
            self::$databaseWasRefreshed = true;
        }

        parent::setUp();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        RefreshDatabaseState::$migrated = false;
        self::$databaseWasRefreshed = false;
    }

    public function test_empty_application_redirects_to_the_installer(): void
    {
        $this->get('/login')
            ->assertRedirect(route('install.index'));

        $this->get(route('install.index'))
            ->assertOk()
            ->assertSee('Install Skuul')
            ->assertSee('System Administrator')
            ->assertSee('Email is optional');
    }

    public function test_installer_creates_the_first_system_administrator_and_campus(): void
    {
        $response = $this->post(route('install.store'), $this->installationData());

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $admin = User::query()->where('email', 'admin@example.test')->firstOrFail();
        $organization = Organization::query()->firstOrFail();
        $school = School::query()->firstOrFail();

        $this->assertSame(AccountStatus::Active, $admin->account_status);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(app(SystemPermissionScope::class)->allows($admin, PlatformPermission::ManagePlatform));
        $this->assertSame($organization->id, $school->organization_id);
        $this->assertDatabaseHas('installations', [
            'installed_by' => $admin->id,
            'organization_id' => $organization->id,
            'school_id' => $school->id,
            'demo_data_loaded' => true,
            'email_configured' => false,
        ]);
        $this->assertDatabaseHas('audit_events', [
            'action' => AuditAction::InstallationCompleted->value,
            'actor_id' => $admin->id,
            'school_id' => $school->id,
        ]);
        $this->assertSame(1, Notice::query()->count());
    }

    public function test_installer_can_complete_without_email_configuration_or_demo_data(): void
    {
        $data = $this->installationData();
        unset($data['load_demo_data']);

        $this->post(route('install.store'), $data)
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('installations', [
            'demo_data_loaded' => false,
            'email_configured' => false,
        ]);
        $this->assertSame(0, Notice::query()->count());
    }

    public function test_completed_installation_cannot_run_again(): void
    {
        $this->post(route('install.store'), $this->installationData())
            ->assertRedirect(route('login'));

        $this->get(route('install.index'))->assertNotFound();
        $this->post(route('install.store'), $this->installationData())->assertNotFound();
        $this->assertSame(1, Installation::withoutGlobalScopes()->count());
    }

    public function test_existing_minimum_application_state_is_marked_as_installed(): void
    {
        Artisan::call('db:seed', [
            '--class' => RunInProductionSeeder::class,
            '--force' => true,
        ]);

        $organization = Organization::factory()->create();
        $school = School::factory()->for($organization)->create();
        $admin = User::factory()->create();

        app(GrantSchoolMembership::class)->grant($admin, $school, primary: true);
        app(GrantSystemRole::class)->grant($admin, Role::PlatformAdmin);

        $this->get('/login')->assertOk();

        $this->assertDatabaseHas('installations', [
            'installed_by' => $admin->id,
            'school_id' => $school->id,
            'demo_data_loaded' => false,
        ]);
    }

    public function test_database_connection_can_be_tested_before_installation(): void
    {
        $driver = (string) config('database.default');
        $connection = (array) config("database.connections.{$driver}", []);

        $this->post(route('install.database.test'), [
            'driver' => $driver,
            'host' => $connection['host'] ?? null,
            'port' => $connection['port'] ?? null,
            'database' => $connection['database'],
            'username' => $connection['username'] ?? null,
            'password' => $connection['password'] ?? null,
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseCount('installations', 0);
    }

    /**
     * @return array<string, mixed>
     */
    private function installationData(): array
    {
        return [
            'admin_name' => 'System Administrator',
            'admin_email' => 'admin@example.test',
            'admin_password' => 'Correct-Horse-Battery-Staple-42',
            'admin_password_confirmation' => 'Correct-Horse-Battery-Staple-42',
            'organization_name' => 'Example Organization',
            'campus_name' => 'Example Campus',
            'campus_initials' => 'EC',
            'campus_address' => '123 Example Street',
            'load_demo_data' => true,
        ];
    }
}
