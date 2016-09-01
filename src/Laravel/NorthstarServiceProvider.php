<?php

namespace DoSomething\Northstar\Laravel;

use DoSomething\Northstar\Northstar;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

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

        // Register the custom user provider.
        $this->app->resolving('auth', function (AuthManager $auth) {
            if (method_exists($auth, 'provider')) {
                return $this->registerProviderForNewLaravel($auth);
            }

            return $this->registerProviderForOldLaravel($auth);
        });

        // Set alias for facade / requesting from app() helper.
        $this->app->alias(Northstar::class, 'northstar');
    }

    /**
     * Register the user provider for Laravel 5.0 or 5.1 API.
     *
     * @param AuthManager $auth
     */
    public function registerProviderForOldLaravel(AuthManager $auth)
    {
        $auth->extend('northstar', function ($app) {
            return new \DoSomething\Northstar\Laravel\NorthstarUserProvider(
                $app['northstar'], $app['hash'], config('auth.model')
            );
        });
    }

    /**
     * Register the user provider for Laravel 5.2 or newer.
     *
     * @param AuthManager $auth
     */
    public function registerProviderForNewLaravel(AuthManager $auth)
    {
        $auth->provider('northstar', function ($app, array $config) {
            return new \DoSomething\Northstar\Laravel\NorthstarUserProvider(
                $app['northstar'], $app['hash'], config('auth.providers.users.model')
            );
        });
    }
}
