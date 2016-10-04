<?php

namespace DoSomething\Gateway\Exceptions;

class ValidationException extends ApiException
{
    /**
     * The validation errors.
     *
     * @var array
     */
    protected $errors;

    /**
     * Make a new API validation exception.
     *
     * @param array $errors
     * @param string $endpoint
     */
    public function __construct($errors, $endpoint)
    {
        $this->errors = $errors;

        parent::__construct($endpoint, 422, 'Validation errors returned.');
    }

    /**
     * Get the errors from the exception.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
