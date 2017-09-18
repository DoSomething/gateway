<?php

namespace DoSomething\Gateway\Laravel;

use Auth;
use Illuminate\Http\Request;
use DoSomething\Gateway\Blink;
use DoSomething\Gateway\Gladiator;
use DoSomething\Gateway\Northstar;
use DoSomething\Gateway\Server\Token;
use Illuminate\Support\ServiceProvider;
use DoSomething\Gateway\Server\GatewayGuard;
use DoSomething\Gateway\Server\GatewayUserProvider;

class GatewayServiceProvider extends ServiceProvider
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

        $this->app->singleton(Blink::class, function () {
            return new Blink(config('services.blink'));
        });

        $this->app->singleton(Gladiator::class, function () {
            return new Gladiator(config('services.gladiator'));
        });

        // Set alias for requesting from app() helper.
        $this->app->alias(Northstar::class, 'gateway.northstar');
        $this->app->alias(Blink::class, 'gateway.blink');
        $this->app->alias(Gladiator::class, 'gateway.gladiator');

        // Backwards-compatibility.
        $this->app->alias(Northstar::class, 'northstar');

        // Register token validator w/ config dependency.
        $this->app->singleton(Token::class, function ($app) {
            return new Token($app[Request::class], config('services.northstar.key'));
        });

        // Register custom Gateway authentication guard.
        Auth::extend('gateway', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            return new GatewayGuard($app[Token::class], $provider, $app[Request::class]);
        });

        // Register custom Gateway user provider.
        Auth::provider('gateway', function ($app, array $config) {
            return new GatewayUserProvider();
        });
    }
}
