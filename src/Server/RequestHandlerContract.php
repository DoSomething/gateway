<?php

namespace DoSomething\Gateway\Server;

interface RequestHandlerContract
{
    /**
     * Return the current web request.
     */
    public function getRequest();
}
