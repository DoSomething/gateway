<?php

namespace DoSomething\GatewayTests\Helpers\Gambit;

use DoSomething\GatewayTests\Helpers\JsonResponse;

class SignupResponse extends JsonResponse
{
    /**
     * Make a new mock Gambit campaign by id response.
     */
    public function __construct($code = 200)
    {
        $body = [];
        $body['success'] = [
            'code' => $code,
            'msg' => 'OK', // Not a real message.
        ];
        parent::__construct($body, $code);
    }
}
