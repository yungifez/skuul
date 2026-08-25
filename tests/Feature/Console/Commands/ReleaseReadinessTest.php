<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;

class ReleaseReadinessTest extends TestCase
{
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
}
