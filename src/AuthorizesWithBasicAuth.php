<?php

namespace DoSomething\Gateway;

trait AuthorizesWithBasicAuth
{
    /**
     * The username for authorizing requests.
     *
     * @var string
     */
    protected $username;

    /**
     * The password for authorizing requests.
     *
     * @var string
     */
    protected $password;

    /**
     * Run custom tasks before making a request.
     *
     * @see RestApiClient@raw
     */
    protected function runAuthorizesWithBasicAuthTasks($method, &$path, &$options, &$withAuthorization)
    {
        // By default, we append the authorization header to every request.
        if ($withAuthorization) {
            $options['auth'] = $this->getCredentials();
        }
    }

    /**
     * Get the authorization credentials for a request
     *
     * @return null|array
     * @throws \Exception
     */
    protected function getCredentials()
    {
        if (empty($this->username) || empty($this->password)) {
            throw new \Exception('Basic authentication requires a $username & $password property.');
        }

        return [$this->username, $this->password];
    }
}
