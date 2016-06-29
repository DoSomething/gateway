<?php

namespace DoSomething\Northstar\Laravel;

use DoSomething\Northstar\NorthstarClient;
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
        $source = realpath(__DIR__ . '/Migrations/');
        if ($this->app instanceof Application && $this->app->runningInConsole()) {
            $this->publishes([$source => database_path('migrations')], 'migrations');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NorthstarClient::class, function () {
            return new LaravelNorthstarClient(config('services.northstar'));
        });

        // Register the custom user provider.
        $this->app->resolving('auth', function (AuthManager $auth) {
            if (method_exists($auth, 'provide')) {
                return $this->registerProviderForNewLaravel($auth);
            }

            return $this->registerProviderForOldLaravel($auth);
        });

        // Set alias for facade / requesting from app() helper.
        $this->app->alias(NorthstarClient::class, 'northstar');
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
        $auth->provide('northstar', function ($app, array $config) {
            return new \DoSomething\Northstar\Laravel\NorthstarUserProvider(
                $app['northstar'], $app['hash'], $config['auth.model']
            );
        });
    }
}
