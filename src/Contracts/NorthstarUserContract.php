<?php

namespace DoSomething\Northstar\Contracts;

use League\OAuth2\Client\Token\AccessToken;

interface NorthstarUserContract
{
    /**
     * Get the Northstar ID for the user.
     *
     * @return string|null
     */
    public function getNorthstarIdentifier();

    /**
     * Save the Northstar ID for the user.
     *
     * @return void
     */
    public function setNorthstarIdentifier($id);

    /**
     * Get the access token for the user.
     *
     * @return AccessToken|null
     */
    public function getOAuthToken();

    /**
     * Save the access token for the user.
     *
     * @param AccessToken $token
     * @return void
     */
    public function setOAuthToken(AccessToken $token);

    /**
     * Clear the access token for the user.
     *
     * @return void
     */
    public function clearOAuthToken();
}
