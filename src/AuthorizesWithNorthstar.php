<?php

namespace DoSomething\Northstar;

use DoSomething\Northstar\Contracts\OAuthRepositoryContract;
use DoSomething\Northstar\Exceptions\UnauthorizedException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;

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
    protected $scope = ['user'];

    /**
     * The authorization server URL (for example, Northstar).
     *
     * @var string
     */
    protected $authorizationServerUrl;

    /**
     * The class name of the OAuth repository.
     *
     * @var string
     */
    protected $repository;

    /**
     * The authorization server URL (for example, Northstar).
     *
     * @var NorthstarOAuthProvider
     */
    private $authorizationServer;

    /**
     * Authorize a user based on the given username & password.
     *
     * @param array $credentials
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function authorizeUser($credentials)
    {
        try {
            $token = $this->getAuthorizationServer()->getAccessToken('password', [
                'username' => $credentials['username'],
                'password' => $credentials['password'],
                'scope' => $this->scope,
            ]);

            $this->getOAuthRepository()->persistUserCredentials(
                $token->getResourceOwnerId(),
                $token->getToken(),
                $token->getRefreshToken(),
                $token->getExpires()
            );

            return $token;
        } catch (IdentityProviderException $e) {
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
            $token = $this->getAuthorizationServer()->getAccessToken('client_credentials', [
                'scope' => $this->scope,
            ]);

            $this->getOAuthRepository()->persistClientCredentials(
                $this->clientId,
                $token->getToken(),
                $token->getExpires()
            );

            return $token;
        } catch (IdentityProviderException $e) {
            return null;
        }
    }

    /**
     * Re-authorize a user based on their stored refresh token.
     *
     * @param AccessToken $token
     * @return mixed
     */
    public function authorizeByRefreshToken(AccessToken $token)
    {
        $newToken = $this->getAuthorizationServer()->getAccessToken('refresh_token', [
            'refresh_token' => $token->getRefreshToken(),
            'scope' => $this->scope,
        ]);

        $this->getOAuthRepository()->persistUserCredentials(
            $newToken->getResourceOwnerId(),
            $newToken->getToken(),
            $newToken->getRefreshToken(),
            $newToken->getExpires()
        );

        return $newToken;
    }

    /**
     * Get the authorization header for a request, if needed.
     * Overrides this empty method in RestApiClient.
     *
     * @param bool $forceRefresh - Should the token be refreshed, even if expiration timestamp hasn't passed?
     * @return null|string
     * @throws \Exception
     */
    protected function getAuthorizationHeader($forceRefresh = false)
    {
        // @TODO: Client token as well.
        $token = $this->getOAuthRepository()->getUserToken();

        // If the token is expired, fetch a new one.
        if($token && ($token->hasExpired() || $forceRefresh)) {
            // @TODO: ...
            $token = $this->authorizeByRefreshToken($token);
        }

        return $this->getAuthorizationServer()->getHeaders($token);
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
        // If we got an "Access Denied" error from an invalid access token, attempt to force-refresh it once.
        if (! empty($response->error) && $response->error === 'access_denied' && $this->getAttempts() < 2) {
            $options['headers']['Authorization'] = $this->getAuthorizationHeader(true);

            return $this->send($method, $path, $options, false);
        }

        throw new UnauthorizedException($endpoint, json_encode($response));
    }

    /**
     * Get the authorization server.
     */
    protected function getAuthorizationServer()
    {
        if (! $this->authorizationServer) {
            $this->authorizationServer = new NorthstarOAuthProvider([
                'url' => $this->authorizationServerUrl,
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'redirectUri' => '', // @TODO: Add this once we support auth code grant.
            ]);
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
}
