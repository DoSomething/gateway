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
     * @return void
     */
    public function persistUserCredentials($userId, $accessToken, $refreshToken, $expiration);

    /**
     * Save the access token for an authorized client.
     *
     * @param $clientId - OAuth client ID
     * @param $accessToken - Encoded OAuth access token
     * @param $expiration - Access token expiration as UNIX timestamp
     * @return void
     */
    public function persistClientCredentials($clientId, $accessToken, $expiration);
}
