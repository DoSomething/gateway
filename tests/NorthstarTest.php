<?php

class AuthorizesWithNorthstarTest extends TestCase
{
    protected $defaultConfig = [
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
     * Test that we can instantiate a client that authorizes with Northstar.
     * @skip
     */
    public function testNorthstarApiClient()
    {
        $this->markTestIncomplete();
    }
}
