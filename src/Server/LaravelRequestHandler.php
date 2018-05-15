<?php

namespace DoSomething\Gateway\Server;

class LaravelRequestHandler implements RequestHandlerContract
{
    /**
     * Return the current web request.
     */
    public function getRequest()
    {
        return request();
    }
}
