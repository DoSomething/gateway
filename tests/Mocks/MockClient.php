<?php

use DoSomething\Gateway\Common\RestApiClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class MockClient extends RestApiClient
{
    /**
     * MockClient constructor.
     *
     * @param string $url
     * @param array $responses - PSR responses to be returned, in order.
     */
    public function __construct($url, $responses)
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);

        parent::__construct($url, ['handler' => $handler]);
    }
}
