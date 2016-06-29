<?php

namespace DoSomething\Northstar;

use DoSomething\Northstar\Contracts\OAuthRepositoryContract;
use DoSomething\Northstar\Exceptions\UnauthorizedException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;

trait AuthorizesWithNorthstar
{
    /**
     * The grant to use for authorization: supported values are either
     * 'password' or 'client_credentials'.
     *
     * @var string
     */
    protected $grant = 'password';

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
     *
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
     * The authorization server.
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
    public function authorizeByPasswordGrant($credentials)
    {
        try {
            $token = $this->getAuthorizationServer()->getAccessToken('password', [
                'username' => $credentials['username'],
                'password' => $credentials['password'],
                'scope' => $this->scope,
            ]);

            $this->getOAuthRepository()->persistUserToken(
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
    public function authorizeByClientCredentialsGrant()
    {
        try {
            $token = $this->getAuthorizationServer()->getAccessToken('client_credentials', [
                'scope' => $this->scope,
            ]);

            $this->getOAuthRepository()->persistClientToken(
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
     * @param AccessToken $oldToken
     * @return AccessToken
     */
    public function authorizeByRefreshTokenGrant(AccessToken $oldToken)
    {
        try {
            $token = $this->getAuthorizationServer()->getAccessToken('refresh_token', [
                'refresh_token' => $oldToken->getRefreshToken(),
                'scope' => $this->scope,
            ]);

            $this->getOAuthRepository()->persistUserToken(
                $token->getResourceOwnerId(),
                $token->getToken(),
                $token->getRefreshToken(),
                $token->getExpires()
            );

            return $token;
        } catch (IdentityProviderException $e) {
            $this->getOAuthRepository()->requestUserCredentials();

            return null;
        }
    }

    /**
     * Invalidate the authenticated user's refresh token.
     */
    public function invalidateCurrentRefreshToken()
    {
        if ($this->grant === 'client_credentials') {
            return;
        }

        $token = $this->getAccessToken();
        if ($token) {
            $this->invalidateRefreshToken($token);
        }
    }

    /**
     * Invalidate the refresh token for the given access token.
     *
     * @param AccessToken $token
     */
    public function invalidateRefreshToken(AccessToken $token)
    {
        $this->getAuthorizationServer()->getAuthenticatedRequest('DELETE',
            $this->authorizationServerUrl . '/v2/auth/token', $token, [
                'json' => [
                    'token' => $token->getRefreshToken(),
                ],
            ]);

        $this->getOAuthRepository()->removeUserToken($token->getResourceOwnerId());
    }

    /**
     * Get the access token from the repository based on the chosen grant.
     *
     * @return mixed
     * @throws \Exception
     */
    protected function getAccessToken()
    {
        switch ($this->grant) {
            case 'client_credentials':
                return $this->getOAuthRepository()->getClientToken();

            case 'password':
                return $this->getOAuthRepository()->getUserToken();

            default:
                throw new \Exception('Unsupported grant type. Check $this->grant.');
        }
    }

    /**
     * Get a new access token based on the chosen grant.
     *
     * @param $token
     * @return mixed
     * @throws \Exception
     */
    protected function refreshAccessToken($token)
    {
        switch ($this->grant) {
            case 'client_credentials':
                return $this->authorizeByClientCredentialsGrant();

            case 'password':
                return $this->authorizeByRefreshTokenGrant($token);

            default:
                throw new \Exception('Unsupported grant type. Check $this->grant.');
        }
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
        $token = $this->getAccessToken();

        // If the token is expired, fetch a new one before making the request.
        if (! $token || ($token && $token->hasExpired()) || $forceRefresh) {
            $token = $this->refreshAccessToken($token);
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
