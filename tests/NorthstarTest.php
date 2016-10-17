<?php

use DoSomething\GatewayTests\Helpers\JsonResponse;
use DoSomething\GatewayTests\Helpers\JwtResponse;
use DoSomething\GatewayTests\Helpers\UserResponse;
use DoSomething\GatewayTests\Helpers\UsersResponse;

class NorthstarTest extends TestCase
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
        $restClient = new MockNorthstar($this->defaultConfig, [/* ... */]);

        $this->assertEquals((string) $restClient->getBaseUri(), 'https://northstar-phpunit.dosomething.org');
    }

    /**
     * Test that we can request an access token and use it to retrieve
     * a page using client credentials grant.
     */
    public function testGetAuthorizedPage()
    {
        $restClient = new MockNorthstar($this->defaultConfig, [
            new JwtResponse(),
            new JsonResponse([
                'status' => 'good',
            ]),
        ]);

        $response = $restClient->get('status');
        $this->assertEquals($response, ['status' => 'good']);
    }

    /**
     * Test that we can use the "all users" endpoint.
     */
    public function testGetAllUsers()
    {
        $restClient = new MockNorthstar($this->defaultConfig, [
            new JwtResponse,
            new UsersResponse,
        ]);

        $response = $restClient->getAllUsers();

        // It should successfully serialize into a collection.
        $this->assertInstanceOf(\DoSomething\Gateway\Common\ApiCollection::class, $response);

        // And we should be able to traverse and read values from that.
        $this->assertEquals('Katherine', $response[0]->first_name);
        $this->assertEquals('Robert', $response[1]->first_name);
        $this->assertEquals(2, $response->count());
    }

    /**
     * Test that we can retrieve a user by their Northstar ID.
     */
    public function testGetUserById()
    {
        $restClient = new MockNorthstar($this->defaultConfig, [
            new JwtResponse,
            new UserResponse,
        ]);

        $response = $restClient->getUser('id', '5480c950ce5fbc2145eb7721');

        $this->assertEquals('Katherine', $response->first_name);
    }

    /**
     * Test that we can retrieve a user by their mobile number.
     */
    public function testGetUserByMobile()
    {
        $restClient = new MockNorthstar($this->defaultConfig, [
            new JwtResponse,
            new UserResponse,
        ]);

        $response = $restClient->getUser('mobile', '5551234567');

        $this->assertEquals('Katherine', $response->first_name);
    }

    /**
     * Test that we can retrieve a user by their email.
     */
    public function testGetUserByEmail()
    {
        $restClient = new MockNorthstar($this->defaultConfig, [
            new JwtResponse,
            new UserResponse,
        ]);

        $response = $restClient->getUser('email', 'kitty@xavierinstitute.edu');

        $this->assertEquals('Katherine', $response->first_name);
    }

    /**
     * Test that we can retrieve a user by their Drupal ID.
     */
    public function testGetUserByDrupalId()
    {
        $restClient = new MockNorthstar($this->defaultConfig, [
            new JwtResponse,
            new UserResponse,
        ]);

        $response = $restClient->getUser('drupal_id', '123456');

        $this->assertEquals('Katherine', $response->first_name);
    }

    /**
     * Test that we can retrieve a user by their Facebook ID.
     */
    public function testGetUserByFacebookId()
    {
        $restClient = new MockNorthstar($this->defaultConfig, [
            new JwtResponse,
            new UserResponse,
        ]);

        $response = $restClient->getUser('facebook_id', '10101010101010101');

        $this->assertEquals('Katherine', $response->first_name);
    }
}
