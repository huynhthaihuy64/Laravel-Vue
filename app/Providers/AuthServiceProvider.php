<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Events\SetRemember;
use App\Listeners\SetTokenExpiration;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->app['events']->listen(SetRemember::class, SetTokenExpiration::class);
        $this->registerPolicies();

        // MISSING HERE
        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
        Passport::tokensExpireIn(now()->addDays(2));
    }
}
