<?php

namespace DoSomething\GatewayTests\Helpers\Blink;

use DoSomething\GatewayTests\Helpers\JsonResponse;

class BlinkResponse extends JsonResponse
{
    /**
     * Make a new mock Gambit campaign by id response.
     */
    public function __construct()
    {
        $body = [];
        $body = [
            'ok' => true,
            'message' => 'Message queued',
            'code' => 'success_message_queued',
        ];
        parent::__construct($body, 201);
    }
}
