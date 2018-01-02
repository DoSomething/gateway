<?php

use Carbon\Carbon;
use DoSomething\Gateway\Server\Token;
use DoSomething\Gateway\Server\Exceptions\AccessDeniedException;

class TokenTest extends TestCase
{
    /** @test */
    public function testNoToken()
    {
        $request = $this->createRequest(null);
        $token = new Token($request, $this->key);

        $this->assertFalse($token->exists());
    }

    /** @test */
    public function testInvalidToken()
    {
        $request = $this->createRequest('Bearer nah');
        $token = new Token($request, $this->key);

        $this->setExpectedException(AccessDeniedException::class);
        $token->exists(); // throws!
    }

    /** @test */
    public function testExpiredToken()
    {
        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('9/14/2017 4:00pm'), []);

        $this->mockTime('9/14/2017 7:00pm');
        $token = new Token($request, $this->key);

        $this->setExpectedException(AccessDeniedException::class);
        $token->exists(); // throws!
    }

    /** @test */
    public function testTokenWithInvalidSignature()
    {
        $other = __DIR__ . '/other-private.key';
        $request = $this->createJwtRequest($other, 'phpunit', new Carbon('9/14/2017 4:00pm'), []);

        $this->mockTime('9/14/2017 4:10pm');
        $token = new Token($request, $this->key);

        $this->setExpectedException(AccessDeniedException::class);
        $token->exists(); // throws!
    }

    /** @test */
    public function testValidClientToken()
    {
        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('9/14/2017 3:55pm'), [
            'scopes' => ['admin'],
        ]);

        $this->mockTime('9/14/2017 4:00pm');
        $token = new Token($request, $this->key);

        $this->assertTrue($token->exists());
        $this->assertEquals('phpunit', $token->client);
        $this->assertEquals(['admin'], $token->scopes);
        $this->assertEquals(null, $token->id);
        $this->assertEquals(null, $token->role);
    }

    /** @test */
    public function testValidUserToken()
    {
        $request = $this->createJwtRequest($this->signer, 'phpunit', new Carbon('9/14/2017 3:55pm'), [
            'sub' => '5543dfd6469c64ec7d8b46b3',
            'role' => 'admin',
            'scopes' => ['role:admin', 'role:staff'],
        ]);

        $this->mockTime('9/14/2017 4:00pm');
        $token = new Token($request, $this->key);

        $this->assertTrue($token->exists());
        $this->assertEquals('phpunit', $token->client);
        $this->assertEquals(['role:admin', 'role:staff'], $token->scopes);
        $this->assertEquals('5543dfd6469c64ec7d8b46b3', $token->id);
        $this->assertEquals('admin', $token->role);
    }
}
