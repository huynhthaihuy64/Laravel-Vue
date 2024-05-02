<?php

namespace App\Providers;

use App\Repositories\PerformanceRepositoryInterface;
use App\Repositories\Eloquent\PerformanceRepository;
use App\Repositories\GoalPerformanceRepositoryInterface;
use App\Repositories\Eloquent\GoalPerformanceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //Example custom Provider for Repository

        // $this->app->bind(PerformanceRepositoryInterface::class, PerformanceRepository::class);
        // $this->app->bind(GoalPerformanceRepositoryInterface::class, GoalPerformanceRepository::class);
    }
}
