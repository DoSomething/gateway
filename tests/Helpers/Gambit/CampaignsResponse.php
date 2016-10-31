<?php

namespace DoSomething\GatewayTests\Helpers\Gambit;

use DoSomething\GatewayTests\Helpers\JsonResponse;

class CampaignsResponse extends JsonResponse
{
    /**
     * Make a new mock Gambit list all campaigns response.
     */
    public function __construct($code = 200)
    {
        $body = [];
        $body['data'][] = [
            'id' => 362,
            'title' => 'World Recycle Week: Close The Loop',
            'tagline' => 'Unplug unused electronics to conserve energy.',
            'status' => 'closed',
            'keywords' => ['CLOTHESBOT'],
        ];
        $body['data'][] = [
            'id' => 876,
            'title' => 'Trash Stash',
            'campaignbot' => true,
            'status' => 'active',
            'current_run' => 6230,
            'mobilecommons_group_doing' => 258142,
            'mobilecommons_group_completed' => 258163,
            'keywords' => ['TRASHBOT'],
        ];
        parent::__construct($body, $code);
    }
}
