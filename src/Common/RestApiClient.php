<?php

namespace DoSomething\Northstar\Common;

use DoSomething\Northstar\Exceptions\ForbiddenException;
use DoSomething\Northstar\Exceptions\InternalException;
use DoSomething\Northstar\Exceptions\UnauthorizedException;
use DoSomething\Northstar\Exceptions\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class RestApiClient
{
    /**
     * The Guzzle HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The number of times a request has been attempted.
     *
     * @var int
     */
    protected $attempts;

    /**
     * RestApiClient constructor.
     *
     * @param string $url - Base URL for this API, e.g. https://api.dosomething.org/
     * @param array $additionalHeaders - Additional headers that should be sent with every request
     */
    public function __construct($url, $additionalHeaders = [])
    {
        $standardHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $client = new Client([
            'base_url' => $url,
            'defaults' => [
                'headers' => array_merge($standardHeaders, $additionalHeaders),
            ],
        ]);

        $this->client = $client;
    }

    /**
     * Send a GET request to the given URL.
     *
     * @param string $path - URL to make request to (relative to base URL)
     * @param array $query - Key-value array of query string values
     * @param bool $withAuthorization - Should this request be authorized?
     * @return array
     */
    public function get($path, $query = [], $withAuthorization = true)
    {
        $options = [
            'query' => $query,
        ];

        $response = $this->send('GET', $path, $options, $withAuthorization);

        return is_null($response) ? null : $response->json();
    }

    /**
     * Send a POST request to the given URL.
     *
     * @param string $path - URL to make request to (relative to base URL)
     * @param array $body - Body of the POST request
     * @param bool $withAuthorization - Should this request be authorized?
     * @return array
     */
    public function post($path, $body = [], $withAuthorization = true)
    {
        $options = [
            'body' => json_encode($body),
        ];

        $response = $this->send('POST', $path, $options, $withAuthorization);

        return is_null($response) ? null : $response->json();
    }

    /**
     * Send a PUT request to the given URL.
     *
     * @param string $path - URL to make request to (relative to base URL)
     * @param array $body - Body of the PUT request
     * @param bool $withAuthorization - Should this request be authorized?
     * @return array
     */
    public function put($path, $body = [], $withAuthorization = true)
    {
        $options = [
            'body' => json_encode($body),
        ];

        $response = $this->send('PUT', $path, $options, $withAuthorization);

        return is_null($response) ? null : $response->json();
    }

    /**
     * Send a DELETE request to the given URL.
     *
     * @param string $path - URL to make request to (relative to base URL)
     * @param bool $withAuthorization - Should this request be authorized?
     * @return bool
     */
    public function delete($path, $withAuthorization = true)
    {
        $response = $this->send('DELETE', $path, $withAuthorization);

        return $this->responseSuccessful($response);
    }

    /**
     * Get the authorization header for a request, if needed.
     * @see AuthorizesWithOAuth
     *
     * @return string|null
     */
    protected function getAuthorizationHeader()
    {
        return null;
    }

    /**
     * Send a Northstar API request, and parse any returned validation
     * errors or status codes to present to the user.
     *
     * @param string $method - 'GET', 'POST', 'PUT', or 'DELETE'
     * @param string $path - URL to make request to (relative to base URL)
     * @param array $options - Guzzle options (http://guzzle.readthedocs.org/en/latest/request-options.html)
     * @param bool $withAuthorization - Should this request be authorized?
     * @return Response|void
     * @throws ForbiddenException
     * @throws InternalException
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    public function send($method, $path, $options = [], $withAuthorization = true)
    {
        // By default, we append the authorization header to every request.
        if ($withAuthorization) {
            $token = $this->getAuthorizationHeader();
            if (! empty($token)) {
                $options['headers']['Authorization'] = $token;
            }
        }

        try {
            // Increment the number of attempts so we can eventually give up.
            $this->attempts++;
            
            // Make the request. Any error code will send us to the 'catch' below.
            $response = $this->raw($method, $path, $options);
            
            // Reset the number of attempts back to zero once we've had a successful response!
            $this->attempts = 0;
            
            return $response;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $endpoint = strtoupper($method).' '.$path;

            switch ($e->getCode()) {
                // If the request is unauthorized, throw a generic unauthorized exception.
                case 401:
                    throw new UnauthorizedException($endpoint, $e->getMessage());
                    break;

                // If the request is forbidden, throw a generic forbidden exception.
                case 403:
                    throw new ForbiddenException($endpoint, $e->getMessage());
                    break;

                // If the resource doesn't exist, return null.
                case 404:
                    return null;
                    break;

                // If it's a validation error, throw a generic validation error.
                case 422:
                    $errors = json_decode($e->getResponse()->getBody()->getContents())->error->fields;
                    throw new ValidationException($errors, $endpoint);
                    break;

                default:
                    throw new InternalException($endpoint, $e->getCode(), $e->getMessage());
            }
        }
    }

    /**
     * Send a raw API request, without attempting to handle error responses.
     *
     * @param $method
     * @param $path
     * @param array $options
     * @return Response|void
     */
    public function raw($method, $path, $options)
    {
        return $this->client->send($this->client->createRequest($method, $path, $options));
    }

    /**
     * Determine if the response was successful or not.
     *
     * @param $response
     * @return bool
     */
    public function responseSuccessful(Response $response)
    {
        return isset($response->json()['success']);
    }

    /**
     * Get the number of times a request has been attempted.
     *
     * @return int
     */
    public function getAttempts()
    {
        return $this->attempts;
    }
}
