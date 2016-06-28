<?php

namespace DoSomething\Northstar\Exceptions;

class BadRequestException extends ApiException
{
    /**
     * Make a new 400 Bad Request API response exception.
     * @param string $message
     */
    public function __construct($endpoint, $message)
    {
        parent::__construct($endpoint, 400, $message);
    }
}
