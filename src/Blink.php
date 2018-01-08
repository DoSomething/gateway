<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Exceptions\ValidationException;

class Blink extends RestApiClient
{
    use AuthorizesWithBasicAuth, ForwardsTransactionIds;

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

        // Set fields for `AuthorizesWithBasicAuth` trait.
        $this->username = $config['user'];
        $this->password = $config['password'];

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
     * Send a Post request Blink /events/user-signup endpoint.
     *
     * To notify Blink that Rogue signup has been created.
     *
     * @param array $signup - The array containing Rogue signup fields.
     * @see  https://github.com/DoSomething/rogue/blob/master/documentation/endpoints/signups.md
     * @return bool
     */
    public function userSignup(array $signup)
    {
        $response = $this->post('v1/events/user-signup', $signup);

        // TODO: throw an exception if the post returns a validation error.
        return $this->responseSuccessful($response);
    }

    /**
     * Send a Post request Blink /events/user-signup endpoint.
     *
     * To notify Blink that Rogue signup post has been created.
     *
     * @param array $signup - The array containing Rogue signup post fields.
     * @see  https://github.com/DoSomething/rogue/blob/master/documentation/endpoints/posts.md
     * @return bool
     */
    public function userSignupPost(array $signupPost)
    {
        $response = $this->post('v1/events/user-signup-post', $signupPost);

        // TODO: throw an exception if the post returns a validation error.
        return $this->responseSuccessful($response);
    }

    /**
     * Handle validation exceptions.
     *
     * @param string $endpoint - The human-readable route that triggered the error.
     * @param array $response - The body of the response.
     * @param string $method - The HTTP method for the request that triggered the error, for optionally resending.
     * @param string $path - The path for the request that triggered the error, for optionally resending.
     * @param array $options - The options for the request that triggered the error, for optionally resending.
     * @return \GuzzleHttp\Psr7\Response|void
     * @throws UnauthorizedException
     */
    public function handleValidationException($endpoint, $response, $method, $path, $options)
    {
        // Hackily format the "message" string in key-value format.
        $message = $response['message'];
        $errors = ['error' => [$message]];

        throw new ValidationException($errors, $endpoint);
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
