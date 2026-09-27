<?php

namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Jetstream\DeleteUser;
use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Events\AccountStatusChanged;
use App\Http\Middleware\EnsureFeatureIsEnabled;
use App\Http\Middleware\RequireActiveSchool;
use App\Listeners\RecordAccountStatusChange;
use App\Listeners\RecordPermissionChanges;
use App\Services\Academic\AcademicPeriodContext;
use App\Services\Authorization\OrganizationPermissionScope;
use App\Services\Authorization\SystemPermissionScope;
use App\Services\Curriculum\InstructionalModelResolver;
use App\Services\Feature\FeatureManager;
use App\Services\School\DomainContext;
use App\Services\School\SchoolContext;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\ComponentContext;

use function Livewire\on;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        // Let the first-run screen render before APP_KEY exists. This key is
        // deliberately ephemeral; the installer replaces it before any
        // application session or user data is created.
        if (!filled(config('app.key'))) {
            $this->app->singleton('encrypter', function (): Encrypter {
                return new Encrypter(random_bytes(32), (string) config('app.cipher'));
            });
        }

        // The address the request came in on is read once, before the school.
        $this->app->scoped(DomainContext::class);

        // One school context per request, shared by every query and policy.
        $this->app->scoped(SchoolContext::class);

        // One academic period per request, resolved after the school.
        $this->app->scoped(AcademicPeriodContext::class);

        // Authorization results must not leak between requests or workers.
        $this->app->scoped(SystemPermissionScope::class);

        // Organization memberships are read once per request.
        $this->app->scoped(OrganizationPermissionScope::class);

        // Feature answers are worked out once per request.
        $this->app->scoped(FeatureManager::class);

        // The instructional model of a cycle is read once per request.
        $this->app->scoped(InstructionalModelResolver::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(100);

        // Global roles use the reserved Spatie system team and grant only
        // permissions assigned to their role.
        Gate::before(function ($user, string $ability): ?bool {
            if (app(SystemPermissionScope::class)->allows($user, $ability)) {
                return true;
            }

            // Global-only permissions cannot be granted in a school team.
            if (PlatformPermission::tryFrom($ability) !== null || OrganizationPermission::tryFrom($ability) !== null) {
                return false;
            }

            return null;
        });

        Event::listen(Registered::class, SendEmailVerificationNotification::class);

        // Sensitive changes go to the audit log as they happen.
        Event::listen(AccountStatusChanged::class, RecordAccountStatusChange::class);
        Event::subscribe(RecordPermissionChanges::class);

        RateLimiter::for('api', fn (Request $request): Limit => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('login', fn (Request $request): Limit => Limit::perMinute(5)->by($request->email.$request->ip()));
        RateLimiter::for('two-factor', fn (Request $request): Limit => Limit::perMinute(5)->by($request->session()->get('login.id')));

        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Jetstream::defaultApiTokenPermissions(['read']);
        Jetstream::permissions(['create', 'read', 'update', 'delete']);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        $this->keepLivewireInsideItsSchool();
    }

    /**
     * Hold every Livewire request to the rules of the page it came from.
     *
     * A screen left open in a tab keeps talking to the server. It must stop
     * when the school turns its feature off, and it must never write into
     * another school after the person switched school in another tab.
     */
    private function keepLivewireInsideItsSchool(): void
    {
        Livewire::addPersistentMiddleware([
            EnsureFeatureIsEnabled::class,
            RequireActiveSchool::class,
        ]);

        on('dehydrate', function (Component $component, ComponentContext $context): void {
            $context->addMemo('school', current_school_id());
        });

        on('hydrate', function (Component $component, array $memo): void {
            abort_if(
                array_key_exists('school', $memo) && $memo['school'] !== current_school_id(),
                409,
                'You switched school in another tab. Reload this page to work in the school you chose.',
            );
        });
    }
}
