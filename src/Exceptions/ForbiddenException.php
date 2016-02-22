<?php

namespace DoSomething\Northstar\Exceptions;

class ForbiddenException extends APIException
{
    /**
     * Make a new 403 Forbidden API response exception.
     * @param string $message
     */
    public function __construct($endpoint, $message)
    {
        parent::__construct($endpoint, 403, $message);
    }
}
