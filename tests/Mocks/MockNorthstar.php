<?php

use DoSomething\Gateway\Northstar;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class MockNorthstar extends Northstar
{
    /**
     * MockClient constructor.
     *
     * @param array $config
     * @param array $responses - PSR responses to be returned, in order.
     */
    public function __construct($config, $responses)
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);

        $config = array_merge($config, ['handler' => $handler]);

        parent::__construct($config, ['handler' => $handler]);
    }
}
