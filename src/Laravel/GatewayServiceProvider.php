<?php

namespace DoSomething\Gateway\Laravel;

use Auth;
use Illuminate\Http\Request;
use DoSomething\Gateway\Blink;
use DoSomething\Gateway\Northstar;
use DoSomething\Gateway\Server\Token;
use Illuminate\Support\ServiceProvider;
use DoSomething\Gateway\Server\GatewayGuard;
use DoSomething\Gateway\Server\GatewayUserProvider;
use DoSomething\Gateway\Server\LaravelRequestHandler;

class GatewayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Allow sample migrations to be published using `php artisan vendor:publish`.
            $this->publishes([realpath(__DIR__ . '/Migrations/') => database_path('migrations')], 'migrations');

            // Register 'key' Artisan command.
            $this->commands([
                \DoSomething\Gateway\Server\Commands\PublicKeyCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Northstar::class, function ($app) {
            $client = new LaravelNorthstar(config('services.northstar'));
            $client->setLogger($app['log']);

            return $client;
        });

        $this->app->bind(Blink::class, function ($app) {
            $client = new Blink(config('services.blink'));
            $client->setLogger($app['log']);

            return $client;
        });

        // Set alias for requesting from app() helper.
        $this->app->alias(Northstar::class, 'gateway.northstar');
        $this->app->alias(Blink::class, 'gateway.blink');

        // Backwards-compatibility.
        $this->app->alias(Northstar::class, 'northstar');

        // Register token validator w/ config dependency.
        $this->app->bind(Token::class, function ($app) {
            $key = config('auth.providers.northstar.key');

            // If not set, check old suggested config location:
            if (! $key) {
                $key = config('services.northstar.key');
            }

            return new Token(new LaravelRequestHandler(), $key);
        });

        // Register custom Gateway authentication guard.
        Auth::extend('gateway', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            return new GatewayGuard($provider, $app[Request::class]);
        });

        // Register custom Gateway user provider.
        Auth::provider('gateway', function ($app, array $config) {
            return new GatewayUserProvider();
        });
    }
}
