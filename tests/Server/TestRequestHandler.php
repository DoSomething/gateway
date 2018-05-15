<?php

use DoSomething\Gateway\Server\RequestHandlerContract;

class TestRequestHandler implements RequestHandlerContract
{
    /**
     * The mocked web request.
     */
    protected $request;

    /**
     * Create a new test handler.
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Return the mocked web request.
     */
    public function getRequest()
    {
        return $this->request;
    }
}
