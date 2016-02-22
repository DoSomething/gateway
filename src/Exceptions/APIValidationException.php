<?php

namespace DoSomething\Northstar\Exceptions;

use Exception;

class APIValidationException extends Exception
{

    protected $errors;

    public function __construct($errors, $endpoint)
    {
        $this->errors = $errors;

        parent::__construct('Validation error in Northstar endpoint: '.$endpoint);
    }

    public function getErrors()
    {
        return $this->errors;
    }

}
