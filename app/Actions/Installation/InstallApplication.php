<?php

namespace App\Actions\Installation;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Authorization\GrantSystemRole;
use App\Actions\Organization\CreateOrganization;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\AccountStatus;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Models\Installation;
use App\Models\School;
use App\Models\User;
use App\Services\Installation\InstallationReadiness;
use Database\Seeders\DemoDataSeeder;
use Database\Seeders\RunInProductionSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallApplication
{
    public function __construct(
        private CreateOrganization $createOrganization,
        private GrantSchoolMembership $grantSchoolMembership,
        private GrantSystemRole $grantSystemRole,
        private RecordAuditEvent $recordAuditEvent,
        private DemoDataSeeder $demoDataSeeder,
        private InstallationReadiness $readiness,
    ) {}

    /**
     * @param array{
     *     admin_name: string,
     *     admin_email: string,
     *     admin_password: string,
     *     organization_name: string,
     *     campus_name: string,
     *     campus_address: string,
     *     campus_country: string,
     *     campus_state: string,
     *     campus_city: string,
     *     campus_postal_code: string,
     *     campus_initials?: string|null,
     *     campus_email?: string|null,
     *     load_demo_data?: bool
     * } $data
     */
    public function install(array $data): Installation
    {
        if (!$this->readiness->ready()) {
            throw new \InvalidArgumentException('Complete the installer checks before continuing.');
        }

        return DB::transaction(function () use ($data): Installation {
            if (Installation::withoutGlobalScopes()->lockForUpdate()->exists()) {
                throw new \InvalidArgumentException('The application is already installed.');
            }

            if (User::query()->exists() || School::query()->exists()) {
                throw new \InvalidArgumentException('This database already contains application data.');
            }

            Artisan::call('db:seed', [
                '--class' => RunInProductionSeeder::class,
                '--force' => true,
            ]);

            $admin = User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
            ]);
            $admin->forceFill([
                'account_status' => AccountStatus::Active,
                'email_verified_at' => now(),
            ])->save();

            $organization = $this->createOrganization->create([
                'name' => $data['organization_name'],
            ], $admin);

            $school = School::create([
                'organization_id' => $organization->id,
                'name' => $data['campus_name'],
                'address' => $data['campus_address'],
                'country' => $data['campus_country'],
                'state' => $data['campus_state'],
                'city' => $data['campus_city'],
                'postal_code' => $data['campus_postal_code'],
                'initials' => $data['campus_initials'] ?? null,
                'email' => $data['campus_email'] ?? null,
                'code' => Str::upper(Str::random(10)),
            ]);

            $this->grantSchoolMembership->grant($admin, $school, primary: true);
            $this->grantSystemRole->grant($admin, Role::PlatformAdmin);

            $demoDataLoaded = (bool) ($data['load_demo_data'] ?? false);

            if ($demoDataLoaded) {
                $this->demoDataSeeder->seedFor($school, $admin);
            }

            $installation = Installation::create([
                'lock_key' => 'application',
                'installed_by' => $admin->id,
                'organization_id' => $organization->id,
                'school_id' => $school->id,
                'demo_data_loaded' => $demoDataLoaded,
                'email_configured' => $this->readiness->emailConfigured(),
                'installed_at' => now(),
            ]);

            $this->recordAuditEvent->record(
                AuditAction::InstallationCompleted,
                $installation,
                [
                    'organization_id' => $organization->id,
                    'school_id' => $school->id,
                    'demo_data_loaded' => $demoDataLoaded,
                ],
                $admin,
                $school,
            );

            return $installation;
        }, attempts: 3);
    }
}
