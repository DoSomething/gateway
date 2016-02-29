<?php

namespace DoSomething\Northstar\Exceptions;

class UnauthorizedException extends APIException
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
