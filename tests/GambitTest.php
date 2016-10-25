<?php

use DoSomething\GatewayTests\Helpers\JsonResponse;
use DoSomething\GatewayTests\Helpers\Gambit\CampaignResponse;
// use DoSomething\GatewayTests\Helpers\Gambit\CampaignsResponse;

class GambitTest extends PHPUnit_Framework_TestCase
{
    protected $defaultConfig = [
        'url' => 'https://gambit-phpunit.dosomething.org', // not a real server!
    ];

    /**
     * Test that we can retrieve a campaign by their ID.
     */
    public function testGetCampaignById()
    {
        $restClient = new MockGambit($this->defaultConfig, [
            new CampaignResponse,
        ]);
        $campaign = $restClient->getCampaign(4944);

        // id
        $this->assertEquals(4944, $campaign->id);

        // rb_verb
        $this->assertEquals('Swappin Stories', $campaign->rb_verb);

        // rb_noun
        $this->assertEquals('Seniors', $campaign->rb_noun);

        // msg_rb_confirmation
        $this->assertEquals(
            'Wait, your Grandma did WHAT? Thanks for playing and swapping stories!',
            $campaign->msg_rb_confirmation
        );

        // title
        $this->assertEquals('Senior Story Swap', $campaign->title);

        // tagline
        $this->assertEquals(
            'Swap stories with an older adult to decrease isolation.',
            $campaign->tagline
        );

        // status
        $this->assertEquals('closed', $campaign->status);

        // msg_ask_quantity
        $this->assertEquals(
            'How many seniors did you swap stories with?',
            $campaign->msg_ask_quantity
        );

        // current_run
        $this->assertEquals(7298, $campaign->current_run);

        // mobilecommons_group_doing
        $this->assertEquals(255742, $campaign->mobilecommons_group_doing);

        // mobilecommons_group_completed
        $this->assertEquals(255724, $campaign->mobilecommons_group_completed);

        // mobilecommons_keywords
        $this->assertInternalType('array', $campaign->mobilecommons_keywords);
        $this->assertContainsOnly('string', $campaign->mobilecommons_keywords);
        $this->assertEquals(['SWAPBOT'], $campaign->mobilecommons_keywords);
    }

}
