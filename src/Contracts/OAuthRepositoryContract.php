<?php

namespace DoSomething\Northstar\Contracts;

interface OAuthRepositoryContract
{
    /**
     * Get the given authenticated user's access token.
     *
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public function getUserToken();

    /**
     * Get the OAuth client's token.
     *
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public function getClientToken();

    /**
     * Save the access & refresh tokens for an authorized user.
     *
     * @param $userId - Northstar user ID
     * @param $accessToken - Encoded OAuth access token
     * @param $refreshToken - Encoded OAuth refresh token
     * @param $expiration - Access token expiration as UNIX timestamp
     * @param $role - Northstar user role
     * @return void
     */
    public function persistUserToken($userId, $accessToken, $refreshToken, $expiration, $role);

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
     * Remove the user's token information when they log out.
     *
     * @param $userId - Northstar user ID
     */
    public function removeUserToken($userId);
}
