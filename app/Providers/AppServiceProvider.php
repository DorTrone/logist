<?php

namespace App\Providers;

use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Paginator::useBootstrapFive();
        Model::preventLazyLoading(!$this->app->isProduction());

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()?->id ?: $request->ip());
        });

        Gate::define('packagesPanel', [UserPolicy::class, 'packagesPanel']);
        Gate::define('visitorsPanel', [UserPolicy::class, 'visitorsPanel']);
        Gate::define('adminPanel', [UserPolicy::class, 'adminPanel']);
        Gate::define('errors', [UserPolicy::class, 'errors']);
        Gate::define('tokens', [UserPolicy::class, 'tokens']);
        Gate::define('warehouses', [UserPolicy::class, 'warehouses']);


        Gate::define('packages', [UserPolicy::class, 'packages']);
        Gate::define('transports', [UserPolicy::class, 'transports']);
        Gate::define('customers', [UserPolicy::class, 'customers']);
        Gate::define('verifications', [UserPolicy::class, 'verifications']);
        Gate::define('contacts', [UserPolicy::class, 'contacts']);

        Gate::define('banners', [UserPolicy::class, 'banners']);
        Gate::define('notifications', [UserPolicy::class, 'notifications']);
        Gate::define('pushNotifications', [UserPolicy::class, 'pushNotifications']);

        Gate::define('tasks', [UserPolicy::class, 'tasks']);
        Gate::define('users', [UserPolicy::class, 'users']);
        Gate::define('configs', [UserPolicy::class, 'configs']);

        Gate::define('ipAddresses', [UserPolicy::class, 'ipAddresses']);
        Gate::define('userAgents', [UserPolicy::class, 'userAgents']);
        Gate::define('authAttempts', [UserPolicy::class, 'authAttempts']);
        Gate::define('visitors', [UserPolicy::class, 'visitors']);
    }
}
