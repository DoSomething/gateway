<?php

namespace DoSomething\GatewayTests\Helpers\Gladiator;

use DoSomething\GatewayTests\Helpers\JsonResponse;

class StoreUserResponse extends JsonResponse
{
    /**
     * Make a new mock Gladiator store user response.
     */
    public function __construct($code = 200)
    {
        $body = [];
        $body['data'] = [
            'id' => '550200bba39awieg467a3cg2',
            'first_name' => null,
            'last_name' => null,
            'email' => null,
            'mobile' => null,
            'signup' => null,
            'reportback' => null,
            'created_at' => '2016-03-08T18:27:10+0000',
            'updated_at' => '2016-03-08T18:27:10+0000',
        ];
        parent::__construct($body, $code);
    }
}
