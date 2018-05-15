<?php

use Carbon\Carbon;
use DoSomething\Gateway\Server\Token;
use DoSomething\Gateway\Server\Middleware\RequireRole;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireRoleTest extends TestCase
{
    /** @test */
    public function testNoToken()
    {
        $this->setExpectedException(AccessDeniedHttpException::class);

        $request = $this->createRequest(null);

        $middleware = new RequireRole(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, function () {
            // ...
        }, 'user');
    }

    /** @test */
    public function testTokenWithoutRole()
    {
        $this->setExpectedException(AccessDeniedHttpException::class);

        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('-10 minutes'), [
            'role' => 'user',
        ]);

        $middleware = new RequireRole(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, function () {
            // ...
        }, 'admin');
    }

    /** @test */
    public function testTokenWithRole()
    {
        // We can use $next & $passed as a spy here.
        $passed = false;
        $next = function () use (&$passed) {
            $passed = true;
        };

        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('-10 minutes'), [
            'role' => 'admin',
        ]);

        $middleware = new RequireRole(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, $next, 'admin');

        $this->assertTrue($passed);
    }

    /** @test */
    public function testTokenWithMultipleRoles()
    {
        // We can use $next & $passed as a spy here.
        $passed = false;
        $next = function () use (&$passed) {
            $passed = true;
        };

        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('-10 minutes'), [
            'role' => 'staff',
        ]);

        $middleware = new RequireRole(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, $next, 'admin', 'staff');

        $this->assertTrue($passed);
    }
}
