<?php

use DoSomething\GatewayTests\Helpers\Gambit\CampaignResponse;
use DoSomething\GatewayTests\Helpers\Gambit\CampaignsResponse;
use DoSomething\GatewayTests\Helpers\Gambit\SignupResponse;

class GambitTest extends PHPUnit_Framework_TestCase
{
    protected $defaultConfig = [
        'url' => 'https://gambit-phpunit.dosomething.org', // not a real server!
    ];
    protected $authorizedConfig = [
        'url'    => 'https://gambit-phpunit.dosomething.org', // not a real server!
        'apiKey' => 'gambit_api_key',
    ];

    /**
     * Test that we can use the all campaigns endpoint.
     */
    public function testGetAllCampaigns()
    {
        $restClient = new MockGambit($this->defaultConfig, [
            new CampaignsResponse,
        ]);

        $campaigns = $restClient->getAllCampaigns();

        // It should successfully serialize into a collection.
        $this->assertInstanceOf(\DoSomething\Gateway\Common\ApiCollection::class, $campaigns);

        // Test correct campaigns count.
        $this->assertEquals(2, $campaigns->count());

        // And we should be able to traverse and read values from that.
        $this->assertEquals('World Recycle Week: Close The Loop', $campaigns[0]->title);
        $this->assertEquals('Trash Stash', $campaigns[1]->title);
    }

    /**
     * Test that we can retrieve a campaign by their ID.
     */
    public function testGetCampaignById()
    {
        $restClient = new MockGambit($this->defaultConfig, [
            new CampaignResponse,
        ]);
        $campaign = $restClient->getCampaign(876);

        // id
        $this->assertEquals(876, $campaign->id);

        // title
        $this->assertEquals('Trash Stash', $campaign->title);

        // status
        $this->assertEquals('active', $campaign->status);

        // current_run
        $this->assertEquals(6230, $campaign->current_run);

        // mobilecommons_group_doing
        $this->assertEquals(258142, $campaign->mobilecommons_group_doing);

        // mobilecommons_group_completed
        $this->assertEquals(258163, $campaign->mobilecommons_group_completed);

        // keywords
        $this->assertInternalType('array', $campaign->keywords);
        $this->assertContainsOnly('string', $campaign->keywords);
        $this->assertEquals(['TRASHBOT'], $campaign->keywords);
    }

    /**
     * Test that we can post a signup.
     */
    public function testCreateSignup()
    {
        $restClient = new MockGambit($this->authorizedConfig, [
            new SignupResponse,
        ]);
        $result = $restClient->createSignup(2309260, 'node/1141');
        $this->assertTrue($result);
    }
}
