<?php

namespace Tests\Feature;

use App\Exceptions\InvalidValueException;
use App\Services\Backup\BackupCatalogue;
use App\Services\Backup\BackupCipher;
use App\Services\Backup\DatabaseDumperRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * A backup leaves the machine locked, is thinned out by a rule, and is
 * restored on purpose rather than in an emergency for the first time.
 */
class BackupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The key the tests lock their backups with.
     */
    private const KEY = 'base64:c2t1dWwtdGVzdC1iYWNrdXAta2V5LTAxMjM0NTY3ODk=';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('backups');
        config([
            'monitoring.backup.disk' => 'backups',
            'monitoring.backup.path' => 'backups',
            'monitoring.backup.key' => self::KEY,
            'monitoring.backup.require_encryption' => true,
            'monitoring.backup.rehearsal.path' => 'restore-rehearsals',
        ]);
    }

    public function test_a_locked_file_comes_back_the_same(): void
    {
        $cipher = app(BackupCipher::class);
        [$plain, $locked, $back] = $this->threeWorkingFiles();
        file_put_contents($plain, str_repeat('every mark and every fee. ', 100000));

        $cipher->encryptFile($plain, $locked);
        $cipher->decryptFile($locked, $back);

        $this->assertSame(md5_file($plain), md5_file($back));
        $this->assertStringNotContainsString('every mark', (string) file_get_contents($locked));
    }

    public function test_a_backup_changed_on_the_way_is_refused(): void
    {
        $cipher = app(BackupCipher::class);
        [$plain, $locked, $back] = $this->threeWorkingFiles();
        file_put_contents($plain, 'the school year');
        $cipher->encryptFile($plain, $locked);

        $bytes = (string) file_get_contents($locked);
        $bytes[40] = $bytes[40] === 'a' ? 'b' : 'a';
        file_put_contents($locked, $bytes);

        $this->expectException(InvalidValueException::class);

        $cipher->decryptFile($locked, $back);
    }

    public function test_another_key_cannot_open_the_backup(): void
    {
        $cipher = app(BackupCipher::class);
        [$plain, $locked, $back] = $this->threeWorkingFiles();
        file_put_contents($plain, 'the school year');
        $cipher->encryptFile($plain, $locked);

        config(['monitoring.backup.key' => 'base64:'.base64_encode('another key entirely, thirty two')]);

        $this->expectException(InvalidValueException::class);

        $cipher->decryptFile($locked, $back);
    }

    public function test_a_file_this_application_did_not_write_is_refused(): void
    {
        [$plain, $locked, $back] = $this->threeWorkingFiles();
        file_put_contents($locked, str_repeat('not a backup at all', 20));

        $this->expectException(InvalidValueException::class);

        app(BackupCipher::class)->decryptFile($locked, $back);
    }

    public function test_a_backup_is_written_locked(): void
    {
        $this->artisan('skuul:backup')->assertSuccessful();

        $written = Storage::disk('backups')->files('backups');

        $this->assertCount(1, $written);
        $this->assertStringEndsWith('.sql.gz.enc', $written[0]);
        $this->assertStringNotContainsString('CREATE TABLE', Storage::disk('backups')->get($written[0]));
    }

    public function test_a_locked_backup_holds_the_whole_database(): void
    {
        $this->artisan('skuul:backup')->assertSuccessful();
        $dump = tempnam(sys_get_temp_dir(), 'test-dump');

        app(BackupCatalogue::class)->pull((string) app(BackupCatalogue::class)->newest(), (string) $dump);
        $sql = (string) file_get_contents((string) $dump);
        unlink((string) $dump);

        $this->assertStringContainsString('CREATE TABLE `users`', $sql);
        $this->assertStringContainsString('CREATE TABLE `schools`', $sql);
    }

    public function test_a_backup_is_refused_when_the_school_has_no_key(): void
    {
        config(['monitoring.backup.key' => null]);

        $this->artisan('skuul:backup')->assertFailed();

        $this->assertSame([], Storage::disk('backups')->files('backups'));
    }

    public function test_a_school_that_accepts_plain_backups_gets_one(): void
    {
        config(['monitoring.backup.key' => null, 'monitoring.backup.require_encryption' => false]);

        $this->artisan('skuul:backup')->assertSuccessful();

        $written = Storage::disk('backups')->files('backups');

        $this->assertStringEndsWith('.sql.gz', $written[0]);
    }

    public function test_the_uploaded_files_go_with_the_database(): void
    {
        Storage::fake('uploads');
        Storage::disk('uploads')->put('school-logos/one.png', 'a picture');
        config(['monitoring.backup.files_disk' => 'uploads']);

        $this->artisan('skuul:backup', ['--with-files' => true])->assertSuccessful();

        $written = Storage::disk('backups')->files('backups');

        $this->assertCount(2, $written);
        $this->assertTrue(collect($written)->contains(fn (string $file): bool => str_contains($file, '.files.zip')));
    }

    public function test_a_school_with_no_uploads_gets_only_the_database(): void
    {
        Storage::fake('uploads');
        config(['monitoring.backup.files_disk' => 'uploads']);

        $this->artisan('skuul:backup', ['--with-files' => true])->assertSuccessful();

        $this->assertCount(1, Storage::disk('backups')->files('backups'));
    }

    public function test_recent_backups_stay_and_old_ones_go(): void
    {
        config(['monitoring.backup.keep_days' => 30, 'monitoring.backup.keep_months' => 12]);

        $files = [
            'backups/skuul-'.now()->subDays(2)->format('Y-m-d').'-013000.sql.gz.enc',
            'backups/skuul-'.now()->subDays(40)->format('Y-m-d').'-013000.sql.gz.enc',
            'backups/skuul-'.now()->subDays(41)->format('Y-m-d').'-013000.sql.gz.enc',
            'backups/skuul-'.now()->subYears(2)->format('Y-m-d').'-013000.sql.gz.enc',
        ];

        $expired = app(BackupCatalogue::class)->expired($files);

        $this->assertNotContains($files[0], $expired, 'A backup from this month must stay.');
        $this->assertContains($files[3], $expired, 'A backup from two years ago must go.');
        // One of the two old ones is the first of its month, so it stays.
        $this->assertCount(1, array_intersect([$files[1], $files[2]], $expired));
    }

    public function test_taking_a_backup_removes_the_ones_the_rule_dropped(): void
    {
        config(['monitoring.backup.keep_days' => 30, 'monitoring.backup.keep_months' => 0]);
        Storage::disk('backups')->put('backups/skuul-'.now()->subYears(2)->format('Y-m-d').'-013000.sql.gz.enc', 'old');

        $this->artisan('skuul:backup')->assertSuccessful();

        $left = Storage::disk('backups')->files('backups');

        $this->assertCount(1, $left);
        $this->assertStringNotContainsString(now()->subYears(2)->format('Y'), $left[0]);
    }

    public function test_a_rehearsal_looks_inside_the_newest_backup(): void
    {
        $this->artisan('skuul:backup')->assertSuccessful();

        $this->artisan('skuul:rehearse-restore', ['--check-only' => true])->assertSuccessful();

        $this->assertCount(1, Storage::disk('backups')->files('restore-rehearsals'));
    }

    public function test_a_rehearsal_with_nothing_to_restore_fails(): void
    {
        $this->artisan('skuul:rehearse-restore')->assertFailed();
    }

    public function test_a_rehearsal_refuses_a_backup_that_is_missing_tables(): void
    {
        $short = tempnam(sys_get_temp_dir(), 'short');
        file_put_contents((string) $short, 'CREATE TABLE `migrations` (id int);');
        $squeezed = (string) $short.'.gz';
        file_put_contents($squeezed, (string) gzencode((string) file_get_contents((string) $short)));
        $locked = (string) $short.'.enc';
        app(BackupCipher::class)->encryptFile($squeezed, $locked);
        Storage::disk('backups')->put('backups/skuul-2026-01-01-013000.sql.gz.enc', (string) file_get_contents($locked));

        $this->artisan('skuul:rehearse-restore')
            ->expectsOutputToContain('users')
            ->assertFailed();

        foreach ([$short, $squeezed, $locked] as $file) {
            @unlink((string) $file);
        }
    }

    public function test_a_backup_is_actually_restored_into_another_database(): void
    {
        config(['database.connections.rehearsal.database' => 'testing_rehearsal']);
        DB::purge('rehearsal');
        DB::connection()->statement('create database if not exists testing_rehearsal');

        $this->artisan('skuul:backup')->assertSuccessful();
        $this->artisan('skuul:rehearse-restore', ['--into' => 'rehearsal'])->assertSuccessful();

        $this->assertGreaterThan(0, DB::connection('rehearsal')->table('migrations')->count());
        $this->assertTrue(DB::connection('rehearsal')->getSchemaBuilder()->hasTable('student_records'));

        DB::connection()->statement('drop database testing_rehearsal');
    }

    public function test_the_mysql_dumper_is_the_one_this_installation_uses(): void
    {
        $dumper = app(DatabaseDumperRegistry::class)->forConnection((string) config('database.default'));

        $this->assertSame('mysql', $dumper->driver());
    }

    public function test_an_engine_with_no_dumper_says_so(): void
    {
        $this->expectException(InvalidValueException::class);

        app(DatabaseDumperRegistry::class)->forDriver('punch-cards');
    }

    /**
     * Make three working files and remove them when the test ends.
     *
     * @return array<int, string>
     */
    private function threeWorkingFiles(): array
    {
        $files = [];

        for ($number = 0; $number < 3; $number++) {
            $files[] = (string) tempnam(sys_get_temp_dir(), 'backup-test');
        }

        $this->beforeApplicationDestroyed(function () use ($files): void {
            foreach ($files as $file) {
                @unlink($file);
            }
        });

        return $files;
    }
}
