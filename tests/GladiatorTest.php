<?php

use DoSomething\GatewayTests\Helpers\Gladiator\StoreUserResponse;
use DoSomething\GatewayTests\Helpers\Gladiator\UnsubscribeUserResponse;

class GladiatorTest extends PHPUnit_Framework_TestCase
{
    protected $defaultConfig = [
        'url' => 'https://gladiator-test.dosomething.org', // not a real server!
    ];
    protected $authorizedConfig = [
        'url' => 'https://gladiator-test.dosomething.org', // not a real server!
        'gladiator_api_key' => 'gladiator_api_key',
    ];

    /**
     * Test that we can store users in Gladiator.
     */
    public function testStoringAUser()
    {
        $restClient = new MockGladiator($this->authorizedConfig, [
            new StoreUserResponse,
        ]);

        $user = $restClient->storeUserInContest('550200bba39awieg467a3cg2', '6749', '2039');

        $this->assertTrue($user);
    }

    public function testUnsubscribingAUser()
    {
        $restClient = new MockGladiator($this->authorizedConfig, [
            new UnsubscribeUserResponse,
        ]);

        $response = $restClient->unsubscribeUser('550200bba39awieg467a3cg2', '6749');

        $this->assertResponseOk();
    }
}
