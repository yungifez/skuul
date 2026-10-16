<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * An install moves to the newest GitHub release only when it is safe: a
 * clean checkout, the same major version, and a backup taken first.
 */
class UpdateApplicationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'release.update_repository' => 'yungifez/skuul',
            'app.maintenance.driver' => 'cache',
            'app.maintenance.store' => 'array',
        ]);
    }

    public function test_check_reports_a_newer_release_and_changes_nothing(): void
    {
        $this->fakeRelease('v3.1.0');
        $this->fakeCheckout('v3.0.2');

        $this->artisan('skuul:update --check')
            ->expectsOutput('Skuul v3.1.0 is out. This install runs v3.0.2.')
            ->expectsOutput('Release notes: https://github.com/yungifez/skuul/releases/tag/v3.1.0')
            ->assertSuccessful();

        $this->assertNothingChanged();
        Http::assertSent(fn ($request) => $request->url() === 'https://api.github.com/repos/yungifez/skuul/releases/latest');
    }

    public function test_an_install_on_the_newest_release_is_left_alone(): void
    {
        $this->fakeRelease('v3.0.2');
        $this->fakeCheckout('V3.0.2');

        $this->artisan('skuul:update --no-backup')
            ->expectsOutput('Skuul V3.0.2 is the newest release.')
            ->assertSuccessful();

        $this->assertNothingChanged();
    }

    public function test_an_install_that_is_not_a_release_checkout_is_refused(): void
    {
        $this->fakeRelease('v3.1.0');
        Process::fake(["*'describe'*" => Process::result(errorOutput: 'fatal: No names found', exitCode: 128)]);

        $this->artisan('skuul:update --no-backup')
            ->expectsOutput('This install is not a git checkout of a Skuul release. Update it by hand.')
            ->assertFailed();

        $this->assertNothingChanged();
    }

    public function test_github_failing_to_answer_changes_nothing(): void
    {
        Http::fake(['api.github.com/*' => Http::response('', 503)]);
        $this->fakeCheckout('v3.0.2');

        $this->artisan('skuul:update --no-backup')->assertFailed();

        $this->assertNothingChanged();
    }

    public function test_a_new_major_version_is_left_to_its_release_notes(): void
    {
        $this->fakeRelease('v4.0.0');
        $this->fakeCheckout('v3.9.9');

        $this->artisan('skuul:update --no-backup')
            ->expectsOutput('v4.0.0 is a new major version. Follow its release notes to update.')
            ->assertFailed();

        $this->assertNothingChanged();
    }

    public function test_local_changes_are_never_thrown_away(): void
    {
        $this->fakeRelease('v3.1.0');
        $this->fakeCheckout('v3.0.2', localChanges: ' M app/Models/User.php');

        $this->artisan('skuul:update --no-backup')
            ->expectsOutput('This checkout has local changes. Commit or remove them, then update again.')
            ->assertFailed();

        $this->assertNothingChanged();
        Process::assertNotRan(fn ($process) => in_array('reset', $process->command, true));
    }

    public function test_a_failed_download_keeps_the_site_up(): void
    {
        $this->fakeRelease('v3.1.0');
        $this->fakeCheckout('v3.0.2', fetchFails: true);

        $this->artisan('skuul:update --no-backup')
            ->expectsOutput('The release could not be downloaded. Nothing was changed.')
            ->assertFailed();

        $this->assertNothingChanged();
    }

    public function test_a_failed_backup_stops_the_update(): void
    {
        Storage::fake('backups');
        config([
            'monitoring.backup.disk' => 'backups',
            'monitoring.backup.key' => null,
            'monitoring.backup.require_encryption' => true,
        ]);
        $this->fakeRelease('v3.1.0');
        $this->fakeCheckout('v3.0.2');

        $this->artisan('skuul:update')
            ->expectsOutput('The backup failed. Nothing was changed.')
            ->assertFailed();

        $this->assertNothingChanged();
    }

    public function test_an_update_runs_every_step_with_the_new_code_and_brings_the_site_back(): void
    {
        $this->fakeRelease('v3.1.0');
        $this->fakeCheckout('v3.0.2');

        $this->artisan('skuul:update --no-backup')
            ->expectsOutput('Skuul is now on v3.1.0.')
            ->assertSuccessful();

        Process::assertRan(fn ($process) => $process->command === ['git', 'checkout', '--quiet', 'v3.1.0']);
        Process::assertRan(fn ($process) => $process->command === ['composer', 'install', '--no-dev', '--optimize-autoloader', '--no-interaction']);
        Process::assertRan(fn ($process) => $process->command === ['npm', 'run', 'build']);
        Process::assertRan(fn ($process) => $process->command === [PHP_BINARY, 'artisan', 'migrate', '--force']);
        Process::assertRan(fn ($process) => $process->command === [PHP_BINARY, 'artisan', 'db:seed', '--class=RunInProductionSeeder', '--force']);
        Process::assertRan(fn ($process) => $process->command === [PHP_BINARY, 'artisan', 'queue:restart']);
        $this->assertFalse($this->app->isDownForMaintenance());
    }

    public function test_a_failed_step_stops_there_and_keeps_the_site_in_maintenance(): void
    {
        $this->fakeRelease('v3.1.0');
        $this->fakeCheckout('v3.0.2', failingStep: "*'composer'*");

        $this->artisan('skuul:update --no-backup')
            ->expectsOutput('Installing PHP packages failed.')
            ->expectsOutput('Your requirements could not be resolved.')
            ->assertFailed();

        Process::assertNotRan(fn ($process) => in_array('migrate', $process->command, true));
        $this->assertTrue($this->app->isDownForMaintenance());
    }

    private function fakeRelease(string $tag): void
    {
        Http::fake(['api.github.com/repos/yungifez/skuul/releases/latest' => Http::response([
            'tag_name' => $tag,
            'html_url' => "https://github.com/yungifez/skuul/releases/tag/$tag",
        ])]);
    }

    private function fakeCheckout(string $tag, string $localChanges = '', bool $fetchFails = false, ?string $failingStep = null): void
    {
        Process::fake(array_filter([
            $failingStep => $failingStep ? Process::result(errorOutput: 'Your requirements could not be resolved.', exitCode: 2) : null,
            "*'describe'*" => Process::result("$tag\n"),
            "*'status'*" => Process::result($localChanges),
            "*'fetch'*" => $fetchFails ? Process::result(errorOutput: 'Could not resolve host', exitCode: 128) : Process::result(),
            '*' => Process::result(),
        ]));
    }

    /**
     * The checkout was not moved and the site never went down.
     */
    private function assertNothingChanged(): void
    {
        Process::assertNotRan(fn ($process) => in_array('checkout', $process->command, true));
        $this->assertFalse($this->app->isDownForMaintenance());
    }
}
