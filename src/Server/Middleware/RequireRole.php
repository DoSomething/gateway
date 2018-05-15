<?php

namespace DoSomething\Gateway\Server\Middleware;

use Closure;
use DoSomething\Gateway\Server\Token;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireRole
{
    /**
     * The JWT token.
     *
     * @var Token
     */
    protected $token;

    /**
     * RequireRole constructor.
     *
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

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
        $role = $this->token->role;

        // Allow the 'admin' scope to grant privileges to clients.
        // @TODO: Remove this after refactoring client_credential tokens.
        if (in_array('admin', $this->token->scopes)) {
            $role = 'admin';
        }

        // If one of the allowed roles was not provided, throw an exception.
        if (! in_array($role, $allowedRoles)) {
            $message = 'Requires one of the following roles: '.implode(', ', $allowedRoles);
            throw new AccessDeniedHttpException($message);
        }

        return $next($request);
    }
}
