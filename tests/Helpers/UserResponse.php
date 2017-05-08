<?php

namespace DoSomething\GatewayTests\Helpers;

class UserResponse extends JsonResponse
{
    /**
     * Make a new mock user response.
     */
    public function __construct($code = 200)
    {
        $body = [
            'data' => [
                'id' => '5480c950ce5fbc2145eb7721',
                'email' => 'kitty@xavierinstitute.edu',
                'mobile' => '5551234567',
                'facebook_id' => '10101010101010101',
                'drupal_id' => '123456',
                'addr_street1' => '1407 Graymalkin Lane',
                'addr_street2' => '',
                'addr_city' => 'Salem Center',
                'addr_state' => 'New York',
                'addr_zip' => '10560',
                'country' => 'US',
                'birthdate' => '1980-01-31',
                'first_name' => 'Katherine',
                'last_name' => 'Pryde',
                'role' => 'user',
                'updated_at' => '2016-10-14T19:33:24+0000',
                'created_at' => '2016-10-14T19:33:24+0000',
            ],
        ];

        parent::__construct($body, $code);
    }
}
