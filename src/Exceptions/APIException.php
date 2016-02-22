<?php

namespace DoSomething\Northstar\Exceptions;

use Exception;

class APIException extends Exception
{
    /**
     * NorthstarException constructor.
     * @param string $endpoint
     * @param int $code
     * @param string $message
     */
    public function __construct($endpoint, $code, $message)
    {
        parent::__construct('Unhandled error in Northstar "'.$endpoint.'" endpoint: ['.$code.'] '.$message);
    }
}
