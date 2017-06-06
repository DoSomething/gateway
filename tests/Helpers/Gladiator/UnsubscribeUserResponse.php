<?php

namespace DoSomething\GatewayTests\Helpers\Gladiator;

use DoSomething\GatewayTests\Helpers\JsonResponse;

class UnsubscribeUserResponse extends JsonResponse
{
    /**
     * Make a new mock Gladiator store user response.
     */
    public function __construct($code = 200)
    {
        $body = ['message' => 'success'];

        parent::__construct($body, $code);
    }
}
