<?php

namespace DoSomething\Gateway\Laravel\Middleware;

use Closure;
use DoSomething\Gateway\Northstar;

class RefreshTokenMiddleware
{
    /**
     * The Northstar API client.
     *
     * @var Northstar
     */
    protected $northstar;

    /**
     * Create a new filter instance.
     *
     * @param  Northstar $northstar
     */
    public function __construct(Northstar $northstar)
    {
        $this->northstar = $northstar;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If a user is logged in with an expired access token, try to refresh it.
        $this->northstar->refreshIfExpired();

        return $next($request);
    }
}
