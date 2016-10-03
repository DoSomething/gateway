<?php

namespace DoSomething\Northstar\Contracts;

use League\OAuth2\Client\Token\AccessToken;

interface OAuthRepositoryContract
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
     * Get the given authenticated user's access token.
     *
     * @param NorthstarUserContract $user
     *
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public function getUserToken(NorthstarUserContract $user);

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
     */
    public function requestUserCredentials();

    /**
     * Convert the given relative path to an absolute URL
     * with the framework's URL generator.
     *
     * @param $url
     * @return string
     */
    public function prepareUrl($url);
}
