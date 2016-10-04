<?php

namespace DoSomething\Gateway\Exceptions;

class UnauthorizedException extends ApiException
{
    /**
     * Make a new 401 Unauthorized API response exception.
     * @param string $message
     */
    public function __construct($endpoint, $message)
    {
        parent::__construct($endpoint, 401, $message);
    }
}
