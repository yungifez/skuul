<?php

namespace App\Console\Commands;

use App\Services\Report\ReportRegistry;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('skuul:release-readiness {--check-backups : Also check backup freshness and restore rehearsal}')]
#[Description('Check the release-readiness gates')]
class ReleaseReadiness extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ReportRegistry $reports): int
    {
        $checks = [
            'retention policy has been approved' => (bool) config('release.retention_policy_approved'),
            'retention policy version is recorded' => (string) config('release.retention_policy_version') !== '',
            'RPO is a positive number of hours' => (int) config('release.rpo_hours') > 0,
            'RTO is a positive number of minutes' => (int) config('release.rto_minutes') > 0,
        ];

        $availableReports = $reports->all();

        foreach ((array) config('release.pilot_report_keys') as $key) {
            $checks["pilot report [$key] is registered"] = array_key_exists($key, $availableReports);
        }

        $failed = false;

        foreach ($checks as $description => $passed) {
            if ($passed) {
                $this->info("PASS: $description");

                continue;
            }

            $this->error("FAIL: $description");
            $failed = true;
        }

        if ($failed) {
            return self::FAILURE;
        }

        if ($this->option('check-backups') && $this->call(CheckBackup::class) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->info('Release-readiness configuration passed.');

        return self::SUCCESS;
    }
}
