<?php

namespace DoSomething\Northstar;

use DoSomething\Northstar\Common\RestApiClient;
use DoSomething\Northstar\Common\Token;
use DoSomething\Northstar\Contracts\OAuthRepositoryContract;
use DoSomething\Northstar\Exceptions\UnauthorizedException;
use DoSomething\Northstar\Exceptions\ValidationException;
use Lcobucci\JWT\Parser;

trait AuthorizesWithNorthstar
{
    /**
     * The OAuth2 client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * The OAuth2 client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * OAuth scopes to request.
     * @var array
     */
    protected $scope = [];

    /**
     * The authorization server URL (for example, Northstar).
     *
     * @var string
     */
    protected $authorizationServerUrl;

    /**
     * The authorization server URL (for example, Northstar).
     *
     * @var RestApiClient
     */
    protected $authorizationServer;

    /**
     * The repository where OAuth details are stored/retrieved.
     *
     * @var \DoSomething\Northstar\Contracts\OAuthRepositoryContract
     */
    protected $repository;

    /**
     * Get the authorization server.
     */
    protected function getAuthorizationServer()
    {
        if (! $this->authorizationServer) {
            $this->authorizationServer = new RestApiClient($this->authorizationServerUrl);
        }

        return $this->authorizationServer;
    }

    /**
     * Get the OAuth repository used for storing & retrieving tokens.
     * @return OAuthRepositoryContract $repository
     * @throws \Exception
     */
    protected function getOAuthRepository()
    {
        if (! class_exists($this->repository)) {
            throw new \Exception('You must provide an implementation of OAuthRepositoryContract to store tokens.');
        }

        return new $this->repository();
    }

    /**
     * Authorize a user based on the given username & password.
     *
     * @param array $credentials
     * @return mixed
     */
    public function authorizeUser($credentials)
    {
        try {
            $response = $this->getAuthorizationServer()->post('v2/auth/token', [
                'grant_type' => 'password',
                'username' => $credentials['username'],
                'password' => $credentials['password'],
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => implode(' ', $this->scope),
            ], false);

            $jwt = (new Parser())->parse($response['access_token']);
            $this->getOAuthRepository()->persistUserCredentials(
                $jwt->getClaim('sub'),
                $response['access_token'],
                $response['refresh_token'],
                $jwt->getClaim('exp')
            );

            return $response;
        } catch (UnauthorizedException $e) {
            return null;
        } catch (ValidationException $e) {
            return null;
        }
    }

    /**
     * Authorize a machine based on the given client credentials.
     *
     * @return mixed
     */
    public function authorizeClient()
    {
        try {
            $response = $this->getAuthorizationServer()->post('v2/auth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => implode(' ', $this->scope),
            ], false);

            $jwt = (new Parser())->parse($response['access_token']);
            $this->getOAuthRepository()->persistClientCredentials(
                $jwt->getClaim('aud'),
                $response['access_token'],
                $jwt->getClaim('exp')
            );

            return $response;
        } catch (UnauthorizedException $e) {
            return null;
        } catch (ValidationException $e) {
            return null;
        }
    }

    /**
     * Re-authorize a user based on their stored refresh token.
     *
     * @param $token
     * @return mixed
     */
    public function reauthorizeUser(Token $token)
    {
        try {
            $response = $this->getAuthorizationServer()->post('v2/auth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->getRefreshToken(),
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => implode(' ', $this->scope),
            ], false);

            $jwt = (new Parser())->parse($response['access_token']);
            $this->getOAuthRepository()->persistUserCredentials(
                $jwt->getClaim('sub'),
                $response['access_token'],
                $response['refresh_token'],
                $jwt->getClaim('exp')
            );

            return $response;
        } catch (UnauthorizedException $e) {
            return null;
        } catch (ValidationException $e) {
            return null;
        }
    }

    /**
     * Get the authorization header that should be used for requests.
     * Overrides RestApiClient's stub getAuthorizationHeader method.
     *
     * @return string|null
     */
    protected function getAuthorizationHeader()
    {
        $token = $this->getOAuthRepository()->getUserToken();

        if (! $token) {
            return null;
        }

        // If access token will expire with in the next minute, fetch a new one & continue with request.
        // @TODO: Also need to be able to handle re-authorizing with client grant here.
        $access_token = $token->getAccessToken();
        if ($token->willExpireSoon()) {
            $access_token = $this->reauthorizeUser($token)['access_token'];
        }

        return 'Bearer '.$access_token;
    }
}
