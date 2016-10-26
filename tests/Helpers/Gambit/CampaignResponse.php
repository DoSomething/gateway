<?php

namespace DoSomething\GatewayTests\Helpers\Gambit;

use DoSomething\GatewayTests\Helpers\JsonResponse;

class CampaignResponse extends JsonResponse
{
    /**
     * Make a new mock Gambit campaign by id response.
     */
    public function __construct($code = 200)
    {
        $body = [];
        $body['data'] = [
            'id' => 4944,
            'rb_verb' => 'Swappin Stories',
            'rb_noun' => 'Seniors',
            'msg_rb_confirmation' => 'Wait, your Grandma did WHAT? Thanks for playing and swapping stories!',
            'title' => 'Senior Story Swap',
            'tagline' => 'Swap stories with an older adult to decrease isolation.',
            'status' => 'closed',
            'msg_ask_quantity' => 'How many seniors did you swap stories with?',
            'current_run' => 7298,
            'mobilecommons_group_doing' => 255742,
            'mobilecommons_group_completed' => 255724,
            'mobilecommons_keywords' => ['SWAPBOT'],
        ];
        parent::__construct($body, $code);
    }
}
