<?php

namespace Tests\Feature;

use App\Actions\Authorization\GrantSystemRole;
use App\Actions\Installation\SeedWorldData;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\AccountStatus;
use App\Enums\AuditAction;
use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Models\Installation;
use App\Models\Notice;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolOperatingProfile;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use Database\Seeders\RunInProductionSeeder;
use Database\Seeders\WorldSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;
use RuntimeException;
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
        $this->seedWorldReferenceData();

        $this->get('/login')
            ->assertRedirect(route('install.index'));

        $this->get(route('install.index'))
            ->assertOk()
            ->assertSee('Install Skuul')
            ->assertSee('System Administrator')
            ->assertSee('Email is optional')
            ->assertDontSee('id="campus_address_line_2"', false)
            ->assertSee('id="campus_city"', false)
            ->assertSee('list="campus_city-options"', false)
            ->assertSee('id="campus_postal_code"', false)
            ->assertSee('id="campus_country"', false)
            ->assertSee('name="locale"', false)
            ->assertSee('English')
            ->assertSee('name="school_language_preset"', false)
            ->assertSee('Classes and sections');
    }

    public function test_installer_blocks_until_country_and_state_data_is_loaded(): void
    {
        $this->get(route('install.index'))
            ->assertOk()
            ->assertSee('Countries and states')
            ->assertSee('Install countries and states');

        $this->post(route('install.store'), $this->installationData())
            ->assertRedirect()
            ->assertSessionHasErrors('installer');

        $this->assertDatabaseCount('installations', 0);
    }

    public function test_location_options_load_states_and_cities_for_a_country(): void
    {
        $countryId = DB::table('countries')->insertGetId([
            'iso2' => 'CA',
            'name' => 'Canada',
            'status' => 1,
            'phone_code' => '1',
            'iso3' => 'CAN',
            'region' => 'Americas',
            'subregion' => 'Northern America',
        ]);

        DB::table('states')->insert([
            'country_id' => $countryId,
            'name' => 'British Columbia',
            'country_code' => 'CAN',
        ]);
        Cache::forget('location-cities-v1-ca');

        $this->get(route('locations.states', ['country' => 'Canada']))
            ->assertOk()
            ->assertJson(['British Columbia']);

        $cities = $this->get(route('locations.cities', ['country' => 'Canada']))
            ->assertOk()
            ->json();

        $this->assertContains('Vancouver', $cities);
    }

    public function test_world_data_setup_logs_the_failure_reason(): void
    {
        Log::spy();

        $this->mock(SeedWorldData::class, function (MockInterface $mock): void {
            $mock->shouldReceive('seed')
                ->once()
                ->andThrow(new RuntimeException('The database user cannot create the states table.'));
        });

        $this->post(route('install.world.setup'))
            ->assertRedirect()
            ->assertSessionHasErrors('world_data');

        Log::shouldHaveReceived('error')
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'Installer world data setup failed.'
                    && $context['reason'] === 'The database user cannot create the states table.'
                    && $context['exception'] instanceof RuntimeException;
            })
            ->once();
    }

    public function test_world_data_seed_preserves_artisan_failure_output(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->andReturn(1);
        Artisan::shouldReceive('output')
            ->once()
            ->andReturn('SQLSTATE[HY000]: General error: 1142 INSERT command denied.');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('INSERT command denied');

        app(SeedWorldData::class)->seed();
    }

    public function test_world_data_seed_temporarily_raises_a_low_memory_limit(): void
    {
        $previousMemoryLimit = ini_get('memory_limit');
        $this->assertIsString($previousMemoryLimit);
        $this->assertNotFalse(ini_set('memory_limit', '128M'));

        Artisan::shouldReceive('call')
            ->once()
            ->withArgs(function (string $command, array $arguments): bool {
                return $command === 'db:seed'
                    && $arguments['--class'] === WorldSeeder::class
                    && $arguments['--force'] === true
                    && ini_get('memory_limit') === '512M';
            })
            ->andReturn(0);

        try {
            app(SeedWorldData::class)->seed();
        } finally {
            ini_set('memory_limit', $previousMemoryLimit);
        }

        $this->assertSame($previousMemoryLimit, ini_get('memory_limit'));
    }

    public function test_installer_creates_the_first_system_administrator_and_campus(): void
    {
        $this->seedWorldReferenceData();

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
        $this->assertSame('123 Example Street', $school->address);
        $this->assertSame('Canada', $school->country);
        $this->assertSame('British Columbia', $school->state);
        $this->assertSame('Vancouver', $school->city);
        $this->assertSame('V6B 1A1', $school->postal_code);
        $this->assertDatabaseHas('school_operating_profiles', [
            'school_id' => $school->id,
            'preset' => 'home_sections',
        ]);
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

    public function test_installer_saves_the_selected_system_language_for_every_request(): void
    {
        $this->seedWorldReferenceData();

        $data = $this->installationData();
        $data['locale'] = 'fr';

        $this->post(route('install.store'), $data)
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('installations', ['locale' => 'fr']);

        $this->get(route('login'))->assertOk();

        $this->assertSame('fr', app()->getLocale());
    }

    public function test_installer_saves_the_selected_school_terminology_pattern(): void
    {
        $this->seedWorldReferenceData();

        $data = $this->installationData();
        $data['school_language_preset'] = 'subject_schedule';

        $this->post(route('install.store'), $data)
            ->assertRedirect(route('login'));

        $profile = SchoolOperatingProfile::query()->firstOrFail();

        $this->assertSame('subject_schedule', $profile->preset);
        $this->assertSame('Academic year', $profile->labels['academic_year']);
        $this->assertSame('Grade', $profile->labels['class_level']);
        $this->assertSame('Homeroom', $profile->labels['section']);
    }

    public function test_installer_rejects_an_unsupported_system_language(): void
    {
        $this->seedWorldReferenceData();

        $data = $this->installationData();
        $data['locale'] = 'xx';

        $this->post(route('install.store'), $data)
            ->assertRedirect()
            ->assertSessionHasErrors('locale');

        $this->assertDatabaseCount('installations', 0);
    }

    public function test_installer_can_complete_without_email_configuration_or_demo_data(): void
    {
        $this->seedWorldReferenceData();

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
        $this->seedWorldReferenceData();

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
            'campus_country' => 'Canada',
            'campus_state' => 'British Columbia',
            'campus_city' => 'Vancouver',
            'campus_postal_code' => 'V6B 1A1',
            'load_demo_data' => true,
        ];
    }

    private function seedWorldReferenceData(): void
    {
        $countryId = DB::table('countries')->insertGetId([
            'iso2' => 'CA',
            'name' => 'Canada',
            'status' => 1,
            'phone_code' => '1',
            'iso3' => 'CAN',
            'region' => 'Americas',
            'subregion' => 'Northern America',
        ]);

        DB::table('states')->insert([
            'country_id' => $countryId,
            'name' => 'British Columbia',
            'country_code' => 'CAN',
        ]);
    }
}
