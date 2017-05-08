<?php

namespace DoSomething\GatewayTests\Helpers;

class UsersResponse extends JsonResponse
{
    /**
     * Make a new mock users response.
     */
    public function __construct($code = 200)
    {
        $body = [
            'data' => [
                [
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
                [
                    'id' => '5480c950bffebc651c8b456f',
                    'email' => 'bobby@xavierinstitute.edu',
                    'mobile' => '5557654321',
                    'facebook_id' => '20202020202020202',
                    'drupal_id' => '654321',
                    'addr_street1' => '1407 Graymalkin Lane',
                    'addr_street2' => '',
                    'addr_city' => 'Salem Center',
                    'addr_state' => 'New York',
                    'addr_zip' => '10560',
                    'country' => 'US',
                    'birthdate' => '1963-01-10',
                    'first_name' => 'Robert',
                    'last_name' => 'Drake',
                    'role' => 'user',
                    'updated_at' => '2016-10-17T19:33:24+0000',
                    'created_at' => '2016-10-17T19:33:24+0000',
                ],
            ],
            'meta' => [
                'pagination' => [
                    'total' => 2,
                    'count' => 2,
                    'per_page' => 15,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => [],
                ],
            ],
        ];

        parent::__construct($body, $code);
    }
}
