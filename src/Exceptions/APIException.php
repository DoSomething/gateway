<?php

namespace DoSomething\Northstar\Exceptions;

use Exception;

class APIException extends Exception
{
    /**
     * Make a new generic API exception.
     *
     * @param string $endpoint
     * @param int $code
     * @param string $message
     */
    public function __construct($endpoint, $code, $message)
    {
        parent::__construct('Exception in Northstar "'.$endpoint.'" endpoint: ['.$code.'] '.$message);
    }
}
