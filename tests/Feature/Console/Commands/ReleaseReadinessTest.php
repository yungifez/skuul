<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;

class ReleaseReadinessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'demo.enabled' => false,
            'monitoring.backup.files_disk' => 'local',
            'release.retention_policy_approved' => true,
            'release.retention_policy_version' => '2026-08',
            'release.rpo_hours' => 24,
            'release.rto_minutes' => 240,
        ]);
    }

    public function test_the_gate_requires_retention_approval(): void
    {
        config(['release.retention_policy_approved' => false]);

        $this->artisan('skuul:release-readiness')->assertFailed();
    }

    public function test_the_gate_passes_with_approved_release_configuration(): void
    {
        config([
            'release.retention_policy_approved' => true,
            'release.retention_policy_version' => '2026-08',
            'release.rpo_hours' => 24,
            'release.rto_minutes' => 240,
        ]);

        $this->artisan('skuul:release-readiness')->assertSuccessful();
    }

    public function test_the_gate_refuses_demo_mode(): void
    {
        config(['demo.enabled' => true]);

        $this->artisan('skuul:release-readiness')
            ->expectsOutput('FAIL: demo mode is disabled')
            ->assertFailed();
    }

    public function test_the_gate_requires_a_configured_file_backup_disk(): void
    {
        config(['monitoring.backup.files_disk' => 'missing-disk']);

        $this->artisan('skuul:release-readiness')
            ->expectsOutput('FAIL: file backup disk is configured')
            ->assertFailed();
    }

    public function test_the_gate_refuses_an_empty_file_backup_disk(): void
    {
        config(['monitoring.backup.files_disk' => '']);

        $this->artisan('skuul:release-readiness')
            ->expectsOutput('FAIL: file backup disk is configured')
            ->assertFailed();
    }
}
