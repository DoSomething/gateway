<?php

use DoSomething\Gateway\Resources\NorthstarUser;
use DoSomething\GatewayTests\Helpers\Blink\BlinkResponse;
use DoSomething\GatewayTests\Helpers\JwtResponse;
use DoSomething\GatewayTests\Helpers\UserResponse;


class BlinkTest extends PHPUnit_Framework_TestCase
{
    protected $authorizedConfig = [
        'url' => 'https://blink-phpunit.dosomething.org', // not a real server!
        'user' => 'blink',
        'password' => 'totallysecret',
    ];

    protected $northstarConfig = [
        'grant' => 'client_credentials',
        'url' => 'https://northstar-phpunit.dosomething.org', // not a real server!
        'bridge' => MockOAuthBridge::class,

        // Then, configure client ID, client secret, and scopes per grant.
        'client_credentials' => [
            'client_id' => 'example',
            'client_secret' => 'secret1',
            'scope' => ['user'],
        ],
    ];

    /**
     * Test that we can post a signup.
     */
    public function testCreateSignup()
    {
        $blinkRestClient = new MockBlink($this->authorizedConfig, [
            new BlinkResponse,
        ]);

        $northstarRestClient = new MockNorthstar($this->northstarConfig, [
            new JwtResponse,
            new UserResponse,
        ]);
        $northstarUser = $northstarRestClient->getUser('email', 'kitty@xavierinstitute.edu');

        $result = $blinkRestClient->userCreate($northstarUser);
        $this->assertTrue($result);
    }
}
