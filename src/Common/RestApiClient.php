<?php

namespace DoSomething\Northstar\Common;

use DoSomething\Northstar\Exceptions\BadRequestException;
use DoSomething\Northstar\Exceptions\ForbiddenException;
use DoSomething\Northstar\Exceptions\InternalException;
use DoSomething\Northstar\Exceptions\UnauthorizedException;
use DoSomething\Northstar\Exceptions\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

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
     * @param string $url - Base URL for this Resource API, e.g. https://api.dosomething.org/
     */
    public function __construct($url)
    {
        $standardHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $client = new Client([
            'base_uri' => $url,
            'defaults' => [
                'headers' => $standardHeaders,
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
        $body = $response->getBody()->getContents();

        return is_null($response) ? null : json_decode($body, true);
    }

    /**
     * Send a POST request to the given URL.
     *
     * @param string $path - URL to make request to (relative to base URL)
     * @param array $payload - Body of the POST request
     * @param bool $withAuthorization - Should this request be authorized?
     * @return array
     */
    public function post($path, $payload = [], $withAuthorization = true)
    {
        $options = [
            'json' => $payload,
        ];

        $response = $this->send('POST', $path, $options, $withAuthorization);
        $body = $response->getBody()->getContents();

        return is_null($response) ? null : json_decode($body, true);
    }

    /**
     * Send a PUT request to the given URL.
     *
     * @param string $path - URL to make request to (relative to base URL)
     * @param array $payload - Body of the PUT request
     * @param bool $withAuthorization - Should this request be authorized?
     * @return array
     */
    public function put($path, $payload = [], $withAuthorization = true)
    {
        $options = [
            'json' => $payload,
        ];

        $response = $this->send('PUT', $path, $options, $withAuthorization);
        $body = $response->getBody()->getContents();

        return is_null($response) ? null : json_decode($body, true);
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
     * @see AuthorizesWithNorthstar
     *
     * @return string|null
     */
    protected function getAuthorizationHeader()
    {
        return null;
    }

    /**
     * Handle unauthorized exceptions.
     *
     * @param $endpoint - The path that
     * @param $response
     * @param $method - The HTTP method for the request that triggered the error, for optionally resending.
     * @param $path - The path for the request that triggered the error, for optionally resending.
     * @param $options - The options for the request that triggered the error, for optionally resending.
     * @return \GuzzleHttp\Psr7\Response|void
     * @throws UnauthorizedException
     */
    public function handleUnauthorizedException($endpoint, $response, $method, $path, $options)
    {
        throw new UnauthorizedException($endpoint, json_encode($response));
    }

    /**
     * Send a Northstar API request, and parse any returned validation
     * errors or status codes to present to the user.
     *
     * @param string $method - 'GET', 'POST', 'PUT', or 'DELETE'
     * @param string $path - URL to make request to (relative to base URL)
     * @param array $options - Guzzle options (http://guzzle.readthedocs.org/en/latest/request-options.html)
     * @param bool $withAuthorization - Should this request be authorized?
     * @return \GuzzleHttp\Psr7\Response|void
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws InternalException
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    public function send($method, $path, $options = [], $withAuthorization = true)
    {
        try {
            // Increment the number of attempts so we can eventually give up.
            $this->attempts++;

            // Make the request. Any error code will send us to the 'catch' below.
            $response = $this->raw($method, $path, $options, $withAuthorization);

            // Reset the number of attempts back to zero once we've had a successful response!
            $this->attempts = 0;

            return $response;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $endpoint = strtoupper($method).' '.$path;
            $response = json_decode($e->getResponse()->getBody()->getContents());

            switch ($e->getCode()) {
                // If the request is bad, throw a generic bad request exception.
                case 400:
                    throw new BadRequestException($endpoint, json_encode($response));

                // If the request is unauthorized, handle it.
                case 401:
                    return $this->handleUnauthorizedException($endpoint, $response, $method, $path, $options);

                // If the request is forbidden, throw a generic forbidden exception.
                case 403:
                    throw new ForbiddenException($endpoint, json_encode($response));

                // If the resource doesn't exist, return null.
                case 404:
                    return null;

                // If it's a validation error, throw a generic validation error.
                case 422:
                    $errors = $response->error->fields;
                    throw new ValidationException($errors, $endpoint);

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
     * @param bool $withAuthorization
     * @return Response
     */
    public function raw($method, $path, $options, $withAuthorization = true)
    {
        // By default, we append the authorization header to every request.
        if ($withAuthorization) {
            $headers = $this->getAuthorizationHeader();
            if (empty($options['headers'])) {
                $options['headers'] = [];
            }

            $options['headers'] = array_merge($options['headers'], $headers);
        }

        return $this->client->request($method, $path, $options);
    }

    /**
     * Determine if the response was successful or not.
     *
     * @param $response
     * @return bool
     */
    public function responseSuccessful(Response $response)
    {
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);

        return ! empty($json['success']);
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
