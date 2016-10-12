<?php

use GuzzleHttp\Psr7\Response;

class JsonResponse extends Response
{
    /**
     * Make a new mock JsonResponse.
     *
     * @param array $body - The contents of the response.
     * @param int $code - The HTTP status code.
     */
    public function __construct(array $body, $code = 200)
    {
        parent::__construct($code, ['Content-Type' => 'application/json'], json_encode($body));
    }
}
