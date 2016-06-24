<?php

namespace DoSomething\Northstar;

use DoSomething\Northstar\Contracts\OAuthRepositoryContract;
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
     * @return string|null
     */
    protected function getAuthorizationHeader()
    {
        // @TODO: Client token as well.
        $token = $this->getOAuthRepository()->getUserToken();

        // If the token is expired, fetch a new one.
        if($token && $token->hasExpired()) {
            // @TODO: ...
            $token = $this->authorizeByRefreshToken($token);
        }

        return $this->getAuthorizationServer()->getHeaders($token);
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
