<?php

namespace App\Actions\Installation;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\Installation;
use App\Models\School;
use App\Models\User;
use App\Services\Installation\InstallationReadiness;
use Illuminate\Support\Facades\DB;

class CompleteExistingInstallation
{
    public function __construct(
        private InstallationReadiness $readiness,
        private RecordAuditEvent $recordAuditEvent,
    ) {}

    /**
     * Record the installation lock when an older deployment already has the
     * minimum usable application state.
     */
    public function recordIfReady(): ?Installation
    {
        if (Installation::isInstalled()) {
            return Installation::withoutGlobalScopes()->first();
        }

        if (!filled(config('app.key'))) {
            return null;
        }

        $candidate = $this->readiness->existingInstallationCandidate();

        if ($candidate === null) {
            return null;
        }

        return DB::transaction(function () use ($candidate): Installation {
            $existing = Installation::withoutGlobalScopes()->lockForUpdate()->first();

            if ($existing !== null) {
                return $existing;
            }

            $admin = User::query()->findOrFail($candidate['admin_id']);
            $school = School::query()->findOrFail($candidate['school_id']);

            $installation = Installation::create([
                'lock_key' => 'application',
                'installed_by' => $admin->id,
                'organization_id' => $candidate['organization_id'],
                'school_id' => $school->id,
                'locale' => config('app.locale'),
                'demo_data_loaded' => false,
                'email_configured' => $this->readiness->emailConfigured(),
                'installed_at' => now(),
            ]);

            $this->recordAuditEvent->record(
                AuditAction::InstallationCompleted,
                $installation,
                [
                    'existing_application' => true,
                    'organization_id' => $candidate['organization_id'],
                    'school_id' => $school->id,
                ],
                $admin,
                $school,
            );

            return $installation;
        }, attempts: 3);
    }
}
