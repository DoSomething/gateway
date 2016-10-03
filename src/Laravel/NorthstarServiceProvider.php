<?php

namespace DoSomething\Northstar\Laravel;

use DoSomething\Northstar\Northstar;
use Illuminate\Support\ServiceProvider;

class NorthstarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Allow sample migrations to be published using `php artisan vendor:publish`.
        $this->publishes([realpath(__DIR__ . '/Migrations/') => database_path('migrations')], 'migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Northstar::class, function () {
            return new LaravelNorthstar(config('services.northstar'));
        });

        // Set alias for facade / requesting from app() helper.
        $this->app->alias(Northstar::class, 'northstar');
    }
}
