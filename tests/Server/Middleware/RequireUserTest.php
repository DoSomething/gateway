<?php

use Carbon\Carbon;
use DoSomething\Gateway\Server\Middleware\RequireUser;
use DoSomething\Gateway\Server\Token;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireUserTest extends TestCase
{
    /** @test */
    public function testNoToken()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $request = $this->createRequest(null);

        $middleware = new RequireUser(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, function () {
            // ...
        });
    }

    /** @test */
    public function testClientToken()
    {
        $this->expectException(AccessDeniedHttpException::class);

        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('-10 minutes'));

        $middleware = new RequireUser(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, function () {
            // ...
        });
    }

    /** @test */
    public function testUserToken()
    {
        // We can use $next & $passed as a spy here.
        $passed = false;
        $next = function () use (&$passed) {
            $passed = true;
        };

        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('-10 minutes'), [
            'sub' => '5543dfd6469c64ec7d8b46b3',
        ]);

        $middleware = new RequireUser(new Token(new TestRequestHandler($request), $this->key));
        $middleware->handle($request, $next);

        $this->assertTrue($passed);
    }
}
