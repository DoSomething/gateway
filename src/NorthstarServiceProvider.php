<?php

namespace DoSomething\Northstar;

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
        // ...
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

        // Set alias for facade / requesting from IoC container
        $this->app->alias(NorthstarClient::class, 'northstar');
    }
}
