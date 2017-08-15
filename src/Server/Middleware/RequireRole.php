<?php

namespace DoSomething\Gateway\Server;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  array $allowedRoles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$allowedRoles)
    {
        // Allow using the 'admin' scope to grant privileges to clients.
        // @TODO: Remove this after refactoring client_credential tokens.
        if (in_array('admin', token()->scopes)) {
            $role = 'admin';
        }

        // If one of the allowed roles was not provided, throw an exception.
        if (! in_array(token()->role, $allowedRoles)) {
            $message = 'Requires one of the following roles: '.implode(', ', $allowedRoles);
            throw new AccessDeniedHttpException($message);
        }

        return $next($request);
    }
}
