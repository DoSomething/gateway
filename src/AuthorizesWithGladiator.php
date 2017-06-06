<?php

namespace DoSomething\Gateway;

trait AuthorizesWithGladiator
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
    protected function runAuthorizesWithGladiatorTasks($method, &$path, &$options, &$withAuthorization)
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
            throw new \Exception('Gladiator API key is not set.');
        }

        return ['X-DS-Gladiator-API-Key' => $this->apiKey];
    }
}
