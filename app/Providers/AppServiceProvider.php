<?php

namespace App\Providers;

use App\Jobs\IJobInterface;
use App\Jobs\Job;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the interface to the implementation
        $this->app->bind(IJobInterface::class, Job::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
