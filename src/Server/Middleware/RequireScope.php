<?php

namespace DoSomething\Gateway\Server;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  array $requestedScopes
     * @return mixed
     */
    public function handle($request, Closure $next, ...$requestedScopes)
    {
        $providedScopes = token()->scopes;

        // If any of the requested scopes were not provided, throw an exception.
        $missingScopes = array_diff($requestedScopes, $providedScopes);
        if (count($missingScopes)) {
            $message = 'Requires a token with the following scopes: '.implode(', ', $missingScopes);
            throw new AccessDeniedHttpException($message);
        }

        return $next($request);
    }
}
