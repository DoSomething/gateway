<?php

namespace DoSomething\Northstar\Exceptions;

class InternalException extends APIException
{
    /**
     * Make a new internal (likely 500) API response exception.
     * @param string $message
     */
    public function __construct($endpoint, $code, $message)
    {
        parent::__construct($endpoint, $code, $message);
    }
}
