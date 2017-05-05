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
            $authorizationHeader = $this->getAuthorizationHeader();
            if (empty($options['headers'])) {
                $options['headers'] = [];
            }

            $options['headers'] = array_merge($this->defaultHeaders, $options['headers'], $authorizationHeader);
        }
    }

    /**
     * Get the authorization header for a request
     *
     * @return null|array
     * @throws \Exception
     */
    protected function getAuthorizationHeader()
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Blink API key is not set.');
        }

        return ['X-Blink-API-KEY' => $this->apiKey];
    }
}
