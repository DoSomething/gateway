<?php

namespace DoSomething\Gateway;

trait AuthorizesWithBlink
{
    /**
     * ApiKey.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Run custom tasks before making a request.
     *
     * @see RestApiClient@raw
     */
    protected function runAuthorizesWithBlinkTasks($method, &$path, &$options, &$withAuthorization)
    {
        // By default, we append the authorization header to every request.
        if ($withAuthorization) {
            $options['auth'] = $this->getAuth();
        }
    }

    /**
     * Get the authorization credentials for a request
     *
     * @return null|array
     * @throws \Exception
     */
    protected function getAuth()
    {
        if (empty($this->auth) || empty($this->auth['user']) || empty($this->auth['password'])) {
            throw new \Exception('Blink authentication is not set.');
        }

        return [$this->auth['user'], $this->auth['password']];
    }
}
