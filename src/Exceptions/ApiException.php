<?php

namespace DoSomething\Gateway\Exceptions;

use Exception;

class ApiException extends Exception
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
        parent::__construct('Exception from "'.$endpoint.'": ['.$code.'] '.$message, $code);
    }
}
