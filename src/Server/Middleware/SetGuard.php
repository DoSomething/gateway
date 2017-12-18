<?php

namespace DoSomething\Gateway\Server\Middleware;

use Closure;

class SetGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string    $guard
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, $guard)
    {
        // Set the preferred guard for this request.
        auth()->shouldUse($guard);

        return $next($request);
    }
}
