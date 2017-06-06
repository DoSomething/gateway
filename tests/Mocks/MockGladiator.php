<?php

use DoSomething\Gateway\Gladiator;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class MockGladiator extends Gladiator
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
