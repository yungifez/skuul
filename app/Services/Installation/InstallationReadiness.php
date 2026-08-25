<?php

namespace App\Services\Installation;

use App\Enums\AccountStatus;
use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class InstallationReadiness
{
    /**
     * @return array<string, array{label: string, passed: bool, detail: string, action: string|null}>
     */
    public function checks(): array
    {
        $database = $this->checkDatabaseConnection();
        $schema = $this->checkSchema();
        $databaseReady = $database['passed'];
        $schemaReady = $schema['passed'];
        $databaseEmpty = $databaseReady && $schemaReady && User::query()->doesntExist()
            && Organization::query()->doesntExist()
            && School::query()->doesntExist();

        return [
            'database' => $database,
            'application_key' => $this->check(
                'Application key',
                filled(config('app.key')),
                'The application key is available.',
                'Set APP_KEY before starting the installer.',
                'Use Generate application key above.',
            ),
            'storage' => $this->checkStorage(),
            'schema' => $schema,
            'database_empty' => $this->check(
                'Empty database',
                $databaseEmpty,
                'No application records will be overwritten.',
                'This database already contains application data. Use a fresh database for installation.',
                'Use a fresh database, or let Skuul detect the existing minimum setup.',
            ),
            'minimum_state' => $this->check(
                'Minimum application state',
                $databaseEmpty || ($databaseReady && $schemaReady && $this->existingInstallationCandidate() !== null),
                'A usable administrator and organization-backed campus are available.',
                'Create an active platform administrator with an active campus membership.',
            ),
        ];
    }

    /**
     * Check whether all required installer checks pass.
     */
    public function ready(): bool
    {
        return collect($this->checks())->every(fn (array $check): bool => $check['passed']);
    }

    /**
     * Email is optional, so this is reported as information instead of a gate.
     */
    public function emailConfigured(): bool
    {
        $mailer = (string) config('mail.default');

        return !in_array($mailer, ['array', 'log'], true)
            && filled(config('mail.from.address'));
    }

    /**
     * Find the first usable existing application state.
     *
     * @return array{admin_id: int, organization_id: int, school_id: int}|null
     */
    public function existingInstallationCandidate(): ?array
    {
        try {
            foreach (['users', 'schools', 'model_has_roles', 'roles', 'role_has_permissions', 'permissions', 'school_memberships'] as $table) {
                if (!Schema::hasTable($table)) {
                    return null;
                }
            }

            if (!Schema::hasColumn('users', 'account_status') || !Schema::hasColumn('schools', 'organization_id')) {
                return null;
            }

            $candidate = DB::table('users')
                ->join('model_has_roles', function ($join): void {
                    $join->on('model_has_roles.model_id', '=', 'users.id')
                        ->where('model_has_roles.model_type', User::class);
                })
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'roles.id')
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->join('school_memberships', 'school_memberships.user_id', '=', 'users.id')
                ->join('schools', 'schools.id', '=', 'school_memberships.school_id')
                ->where('users.account_status', AccountStatus::Active->value)
                ->whereNull('users.deleted_at')
                ->where('model_has_roles.school_id', SystemPermissionScope::SystemTeamId)
                ->where('roles.name', Role::PlatformAdmin->value)
                ->whereNull('roles.school_id')
                ->where('permissions.name', PlatformPermission::ManagePlatform->value)
                ->where('school_memberships.status', 'active')
                ->whereNotNull('schools.organization_id')
                ->orderBy('users.id')
                ->orderBy('schools.id')
                ->select([
                    'users.id as admin_id',
                    'schools.organization_id',
                    'schools.id as school_id',
                ])
                ->first();
        } catch (Throwable) {
            return null;
        }

        if ($candidate === null) {
            return null;
        }

        return [
            'admin_id' => (int) $candidate->admin_id,
            'organization_id' => (int) $candidate->organization_id,
            'school_id' => (int) $candidate->school_id,
        ];
    }

    /**
     * @return array{label: string, passed: bool, detail: string, action: string|null}
     */
    private function checkDatabaseConnection(): array
    {
        try {
            DB::connection()->getPdo();

            return $this->check(
                'Database connection',
                true,
                'The application can connect to the configured database.',
                '',
            );
        } catch (Throwable) {
            return $this->check(
                'Database connection',
                false,
                '',
                'Check the database settings in your environment configuration.',
                'Edit the database settings above, then test the connection.',
            );
        }
    }

    /**
     * @return array{label: string, passed: bool, detail: string, action: string|null}
     */
    private function checkSchema(): array
    {
        try {
            $ready = collect(['users', 'organizations', 'schools', 'installations'])
                ->every(fn (string $table): bool => Schema::hasTable($table));

            return $this->check(
                'Application schema',
                $ready,
                'The database migrations have been applied.',
                'Run the application migrations, then reload this page.',
                'Use Set up database below.',
            );
        } catch (Throwable) {
            return $this->check(
                'Application schema',
                false,
                '',
                'The application could not inspect the database schema.',
                'Test the database settings, then use Set up database.',
            );
        }
    }

    /**
     * @return array{label: string, passed: bool, detail: string, action: string|null}
     */
    private function checkStorage(): array
    {
        $paths = [storage_path(), storage_path('framework'), storage_path('framework/cache')];
        $writable = collect($paths)->every(fn (string $path): bool => is_dir($path) && is_writable($path));

        return $this->check(
            'Writable storage',
            $writable,
            'The application storage directories are writable.',
            'Make the storage directories writable, then reload this page.',
            'Run: chmod -R ug+rw storage bootstrap/cache',
        );
    }

    /**
     * @return array{label: string, passed: bool, detail: string, action: string|null}
     */
    private function check(
        string $label,
        bool $passed,
        string $success,
        string $failure,
        ?string $action = null,
    ): array {
        return [
            'label' => $label,
            'passed' => $passed,
            'detail' => $passed ? $success : $failure,
            'action' => $passed ? null : $action,
        ];
    }
}
