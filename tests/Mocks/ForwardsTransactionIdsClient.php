<?php

use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\ForwardsTransactionIds;
use GuzzleHttp\Handler\MockHandler;

class ForwardsTransactionIdsClient extends RestApiClient
{
    use ForwardsTransactionIds;

    /**
     * Logged requests this client has made.
     *
     * @var array
     */
    protected $requests = [];

    /**
     * MockClient constructor.
     *
     * @param array $config
     * @param array $responses - PSR responses to be returned, in order.
     */
    public function __construct($responses)
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);

        $history = Middleware::history($this->requests);
        $handler->push($history);

        parent::__construct('http://example.com', ['handler' => $handler]);
    }

    /**
     * Get all requests made by this client.
     *
     * @return \GuzzleHttp\Psr7\Request[]
     */
    public function getRequests()
    {
        return array_map(function ($item) {
            return $item['request'];
        }, $this->requests);
    }

    /**
     * Get the most recent request made by this client.
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getLastRequest()
    {
        return end($this->requests)['request'];
    }
}
