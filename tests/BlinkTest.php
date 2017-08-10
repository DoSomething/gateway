<?php

use DoSomething\Gateway\Blink;
use DoSomething\GatewayTests\Helpers\Blink\BlinkResponse;
use DoSomething\GatewayTests\Helpers\JwtResponse;
use DoSomething\GatewayTests\Helpers\UserResponse;

class BlinkTest extends PHPUnit_Framework_TestCase
{
    protected $authorizedConfig = [
        'url' => 'https://blink-phpunit.dosomething.org', // not a real server!
        'user' => 'blink',
        'password' => 'blink',
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
     * Test that we can notify blink of user creation.
     */
    public function testUserCreate()
    {
        $blinkRestClient = new MockBlink($this->authorizedConfig, [
            new BlinkResponse,
        ]);

        $northstarRestClient = new MockNorthstar($this->northstarConfig, [
            new JwtResponse,
            new UserResponse,
        ]);
        $northstarUser = $northstarRestClient->getUser('email', 'kitty@xavierinstitute.edu');

        $result = $blinkRestClient->userCreate($northstarUser->toArray());
        $this->assertTrue($result);
    }

    /**
     * Test that we can notify blink of user campaign signup.
     */
    public function testUserSigup()
    {
        // Input data.
        $rogueSignupPayload = [
            'id' => 4036838,
            'northstar_id' => '598ca42c10707d7680749f81',
            'campaign_id' => 7,
            'campaign_run_id' => 7818,
            'quantity' => 12,
            'quantity_pending' => null,
            'why_participated' => 'I love to test!',
            'source' => 'niche',
            'created_at' => '2017-08-10 18:21:35',
            'updated_at' => '2017-08-10 18:21:35',
        ];

        $blinkRestClient = new MockBlink($this->authorizedConfig, [
            new BlinkResponse,
        ]);

        $result = $blinkRestClient->userSignup($rogueSignupPayload);
        $this->assertTrue($result);
    }

    /**
     * Test that we can notify blink of user campaign reporback.
     */
    public function testUserReportback()
    {
        // Input data.
        $rogueReportbackPayload = [
            'id' => 4036838,
            'northstar_id' => '598ca42c10707d7680749f81',
            'campaign_id' => 7,
            'campaign_run_id' => 7818,
            'quantity' => 12,
            'quantity_pending' => null,
            'why_participated' => 'I love to test!',
            'source' => 'niche',
            'created_at' => '2017-08-10 18:21:35',
            'updated_at' => '2017-08-10 18:21:35',
        ];

        $blinkRestClient = new MockBlink($this->authorizedConfig, [
            new BlinkResponse,
        ]);

        $result = $blinkRestClient->userReporback($rogueReportbackPayload);
        $this->assertTrue($result);
    }
}
