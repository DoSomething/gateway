<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Resources\NorthstarUser;

class Blink extends RestApiClient
{
    use AuthorizesWithBlink;

    /**
     * Configuration array.
     *
     * @var string
     */
    protected $config;

    /**
     * Default headers applied to every request.
     *
     * @var array
     */
    protected $defaultHeaders;

    /**
     * Create a new Blink API client.
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        // Save configuration.
        $this->config = $config;

        // Set response header.
        $this->auth = [];
        if (! empty($config['user'])) {
            $this->auth['user'] = $config['user'];
        }
        if (! empty($config['password'])) {
            $this->auth['password'] = $config['password'];
        }
        parent::__construct($config['url'], $overrides);
    }

    /**
     * Send a Post request Blink /events/user-create endpoint.
     *
     * To notify Blink that Northstar user has been created.
     *
     * @param array $user - The array containing Northstar user fields.
     * @return bool
     */
    public function userCreate(array $user)
    {
        $response = $this->post('v1/events/user-create', $user);
        // TODO: throw an exception if the post returns a validation error.
        return $this->responseSuccessful($response);
    }

    /**
     * Determine if the response was successful or not.
     *
     * @param mixed $json
     * @return bool
     */
    public function responseSuccessful($json)
    {
        return ! empty($json['ok']) && $json['ok'] === true;
    }
}
