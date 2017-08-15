<?php

namespace DoSomething\Gateway\Server;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If there isn't an ID on the provided token, throw exception.
        if (! token()->id) {
            throw new AccessDeniedHttpException('A user must be logged-in to do that.');
        }

        return $next($request);
    }
}
