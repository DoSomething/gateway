<?php

namespace DoSomething\GatewayTests\Helpers\Gambit;

use DoSomething\GatewayTests\Helpers\JsonResponse;

class CampaignMessageResponse extends JsonResponse
{
    /**
     * Make a new mock Gambit campaign message action response.
     */
    public function __construct($input)
    {
        $body = [];
        $code = 500;
        if (empty($input['id']) || empty($input['phone']) || empty($input['type'])) {
            $code = 422;
            $body['error'] = [
                'error' => $code,
                'message' => 'Missing required parameter',
                'fields' => [],
            ];
        } else {
            $code = 200;
            $msg = 'Sent text for ' . $input['id'] . ' ' . $input['type']
                   . ' to ' . $input['phone'];
            $body['success'] = [
                'code' => $code,
                'message' => $msg,
            ];
        }
        parent::__construct($body, $code);
    }
}
