<?php

namespace DoSomething\Gateway\Exceptions;

use Exception;

class ApiException extends Exception
{
    /**
     * The endpoint that triggered the error.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Make a new generic API exception.
     *
     * @param string $endpoint
     * @param int $code
     * @param string $message
     */
    public function __construct($endpoint, $code, $message)
    {
        $this->endpoint = $endpoint;

        parent::__construct('Exception from "'.$endpoint.'": ['.$code.'] '.$message, $code);
    }

    /**
     * Return the endpoint which triggered the error.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
