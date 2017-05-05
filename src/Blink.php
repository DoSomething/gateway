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
        if (! empty($config['apiKey'])) {
            $this->apiKey = $config['apiKey'];
        }
        parent::__construct($config['url'], $overrides);
    }

    /**
     * Send a Post request Blink /events/user-create endpoint.
     *
     * To notify Blink that user has been created.
     *
     * @param string $id - Signup
     * @return bool
     */
    public function userCreate(NorthstarUser $user)
    {
        $response = $this->post('v1/events/user-create', $user->toArray());
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
