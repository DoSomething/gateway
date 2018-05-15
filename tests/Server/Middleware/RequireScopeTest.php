<?php

use Carbon\Carbon;
use DoSomething\Gateway\Server\Token;
use DoSomething\Gateway\Server\Middleware\RequireScope;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireScopeTest extends TestCase
{
    /** @test */
    public function testNoToken()
    {
        // We can use $next & $passed as a spy here.
        $passed = false;
        $next = function () use (&$passed) {
            $passed = true;
        };

        $request = $this->createRequest(null);

        $middleware = new RequireScope(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, $next, 'user');

        // Since we don't have a token on the request, this should still pass!
        $this->assertTrue($passed);
    }

    /** @test */
    public function testTokenWithoutScope()
    {
        $this->setExpectedException(AccessDeniedHttpException::class);

        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('-10 minutes'), [
            'scopes' => [],
        ]);

        $middleware = new RequireScope(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, function () {
            // ...
        }, 'user');
    }

    /** @test */
    public function testTokenWithScope()
    {
        // We can use $next & $passed as a spy here.
        $passed = false;
        $next = function () use (&$passed) {
            $passed = true;
        };

        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('-10 minutes'), [
            'scopes' => ['user'],
        ]);

        $middleware = new RequireScope(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, $next, 'user');

        $this->assertTrue($passed);
    }

    /** @test */
    public function testTokenWithMultipleScopes()
    {
        // We can use $next & $passed as a spy here.
        $passed = false;
        $next = function () use (&$passed) {
            $passed = true;
        };

        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('-10 minutes'), [
            'scopes' => ['user', 'dog', 'cat', 'puppet'],
        ]);

        $middleware = new RequireScope(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, $next, 'user', 'puppet');

        $this->assertTrue($passed);
    }
}
