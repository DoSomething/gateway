<?php

namespace DoSomething\GatewayTests\Helpers\Gambit;

use DoSomething\GatewayTests\Helpers\JsonResponse;

class CampaignsResponse extends JsonResponse
{
    /**
     * Make a new mock user response.
     */
    public function __construct($code = 200)
    {
        $body = ['data' => [
            [
                'id' => 46,
                'rb_noun' => 'Vampires',
                'rb_verb' => 'Unplugged',
                'msg_rb_confirmation' => 'You SLAYED this campaign. Badass. ',
                'title' => 'Don\'t Be a Sucker',
                'tagline' => 'Unplug unused electronics to conserve energy.',
                'status' => '6196',
                'current_run' => 6196,
                'mobilecommons_group_doing' => 255889,
                'mobilecommons_group_completed' => 255886,
                'mobilecommons_keywords' => ['suckerbot'],
            ],
            [
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
            ],
        ]];

        parent::__construct($body, $code);
    }
}
