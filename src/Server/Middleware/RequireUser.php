<?php

namespace DoSomething\Gateway\Server\Middleware;

use Closure;
use DoSomething\Gateway\Server\Token;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireUser
{
    /**
     * The JWT token.
     *
     * @var Token
     */
    protected $token;

    /**
     * RequireUser constructor.
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If there isn't an ID on the provided token, throw exception.
        if (! $this->token->id) {
            throw new AccessDeniedHttpException('A user must be logged-in to do that.');
        }

        return $next($request);
    }
}
