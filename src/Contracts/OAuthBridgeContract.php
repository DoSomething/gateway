<?php

namespace DoSomething\Gateway\Contracts;

use League\OAuth2\Client\Token\AccessToken;

interface OAuthBridgeContract
{
    /**
     * Get the ID of the logged-in user.
     *
     * @return NorthstarUserContract|null
     */
    public function getCurrentUser();

    /**
     * Get a user by their Northstar ID.
     *
     * @return NorthstarUserContract|null
     */
    public function getUser($id);

    /**
     * Find or create a local user with the given Northstar ID.
     *
     * @param $id
     * @return NorthstarUserContract
     */
    public function getOrCreateUser($id);

    /**
     * Get the OAuth client's token.
     *
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public function getClientToken();

    /**
     * Save the access & refresh tokens for an authorized user.
     *
     * @param \League\OAuth2\Client\Token\AccessToken $token
     * @internal param $userId - Northstar user ID
     */
    public function persistUserToken(AccessToken $token);

    /**
     * Save the access token for an authorized client.
     *
     * @param $clientId - OAuth client ID
     * @param $accessToken - Encoded OAuth access token
     * @param $expiration - Access token expiration as UNIX timestamp
     * @param $role - The role from the JWT
     * @return void
     */
    public function persistClientToken($clientId, $accessToken, $expiration, $role);

    /**
     * If a refresh token is invalid, request the user's credentials
     * by redirecting to the login screen.
     *
     * @return void
     */
    public function requestUserCredentials();

    /**
     * Save the OAuth state token to the session.
     *
     * @param $state
     * @return void
     */
    public function saveStateToken($state);

    /**
     * Get a stored OAuth state token from the session.
     *
     * @return string
     */
    public function getStateToken();

    /**
     * Create a session for the given user & access token.
     *
     * @param NorthstarUserContract $user
     * @param AccessToken $token
     * @return mixed
     */
    public function login(NorthstarUserContract $user, AccessToken $token);

    /**
     * Destroy the current user session.
     *
     * @return mixed
     */
    public function logout();

    /**
     * Convert the given relative path to an absolute URL
     * with the framework's URL generator.
     *
     * @param $url
     * @return string
     */
    public function prepareUrl($url);
}
