<?php

use DoSomething\Gateway\Contracts\NorthstarUserContract;
use DoSomething\Gateway\Contracts\OAuthBridgeContract;
use League\OAuth2\Client\Token\AccessToken;

class MockOAuthBridge implements OAuthBridgeContract
{
    /**
     * In-memory store for the access token.
     *
     * @var AccessToken
     */
    private $token = null;

    /**
     * Get the ID of the logged-in user.
     *
     * @return NorthstarUserContract|null
     */
    public function getCurrentUser()
    {
        // TODO: Implement getCurrentUser() method.
    }

    /**
     * Get a user by their Northstar ID.
     *
     * @return NorthstarUserContract|null
     */
    public function getUser($id)
    {
        // TODO: Implement getUser() method.
    }

    /**
     * Find or create a local user with the given Northstar ID.
     *
     * @param $id
     * @return NorthstarUserContract
     */
    public function getOrCreateUser($id)
    {
        // TODO: Implement getOrCreateUser() method.
    }

    /**
     * Get the OAuth client's token.
     *
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public function getClientToken()
    {
        return $this->token;
    }

    /**
     * Save the access & refresh tokens for an authorized user.
     *
     * @param \League\OAuth2\Client\Token\AccessToken $token
     * @internal param $userId - Northstar user ID
     */
    public function persistUserToken(AccessToken $token)
    {
        $this->token = $token;
    }

    /**
     * Save the access token for an authorized client.
     *
     * @param $clientId - OAuth client ID
     * @param $accessToken - Encoded OAuth access token
     * @param $expiration - Access token expiration as UNIX timestamp
     * @param $role - The role from the JWT
     * @return void
     */
    public function persistClientToken($clientId, $accessToken, $expiration, $role)
    {
        // TODO: Implement persistClientToken() method.
    }

    /**
     * If a refresh token is invalid, request the user's credentials
     * by redirecting to the login screen.
     *
     * @return void
     */
    public function requestUserCredentials()
    {
        // TODO: Implement requestUserCredentials() method.
    }

    /**
     * Save the OAuth state token to the session.
     *
     * @param $state
     * @return void
     */
    public function saveStateToken($state)
    {
        // TODO: Implement saveStateToken() method.
    }

    /**
     * Get a stored OAuth state token from the session.
     *
     * @return string
     */
    public function getStateToken()
    {
        // TODO: Implement getStateToken() method.
    }

    /**
     * Create a session for the given user & access token.
     *
     * @param NorthstarUserContract $user
     * @param AccessToken $token
     * @return mixed
     */
    public function login(NorthstarUserContract $user, AccessToken $token)
    {
        // TODO: Implement login() method.
    }

    /**
     * Destroy the current user session.
     *
     * @return mixed
     */
    public function logout()
    {
        // TODO: Implement logout() method.
    }

    /**
     * Convert the given relative path to an absolute URL
     * with the framework's URL generator.
     *
     * @param $url
     * @return string
     */
    public function prepareUrl($url)
    {
        // TODO: Implement prepareUrl() method.
    }
}
