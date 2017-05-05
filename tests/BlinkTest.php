<?php

use DoSomething\GatewayTests\Helpers\Blink\BlinkResponse;

class BlinkTest extends PHPUnit_Framework_TestCase
{
    protected $authorizedConfig = [
        'url' => 'https://blink-phpunit.dosomething.org', // not a real server!
        'user' => 'blink',
        'password' => 'totallysecret',
    ];

    /**
     * Test that we can post a signup.
     */
    public function testCreateSignup()
    {
        $restClient = new MockBlink($this->authorizedConfig, [
            new BlinkResponse,
        ]);
        $result = $restClient->userCreate(
            // northstar user
        );
        $this->assertTrue($result);
    }
}
