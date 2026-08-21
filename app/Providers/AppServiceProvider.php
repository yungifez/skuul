<?php

namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Jetstream\DeleteUser;
use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Events\AccountStatusChanged;
use App\Listeners\RecordAccountStatusChange;
use App\Listeners\RecordPermissionChanges;
use App\Services\Academic\AcademicPeriodContext;
use App\Services\Authorization\OrganizationPermissionScope;
use App\Services\Authorization\SystemPermissionScope;
use App\Services\Feature\FeatureManager;
use App\Services\School\SchoolContext;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
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
    }
}
