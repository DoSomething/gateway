<?php

namespace DoSomething\Gateway;

trait AuthorizesWithApiKey
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
    protected function runAuthorizesWithApiKeyTasks($method, &$path, &$options, &$withAuthorization)
    {
        // By default, we append the authorization header to every request.
        if ($withAuthorization) {
            $authorizationHeader = $this->getAuthorizationHeader();
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
        if (empty($this->apiKeyHeader) || empty($this->apiKey)) {
            throw new \Exception('API key is not set.');
        }

        return [$this->apiKeyHeader => $this->apiKey];
    }
}
