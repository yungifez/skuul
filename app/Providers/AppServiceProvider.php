<?php

namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Jetstream\DeleteUser;
use App\Events\AccountStatusChanged;
use App\Listeners\RecordAccountStatusChange;
use App\Listeners\RecordPermissionChanges;
use App\Services\Academic\AcademicPeriodContext;
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

        // Feature answers are worked out once per request.
        $this->app->scoped(FeatureManager::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(100);

        // Platform access sits outside school roles. A school role name must
        // never grant access above its own school.
        Gate::after(function ($user): ?bool {
            return $user->isPlatformAdmin() ? true : null;
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
