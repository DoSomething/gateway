<?php

namespace DoSomething\Northstar\Laravel;

use League\OAuth2\Client\Token\AccessToken;

/**
 * @property string $northstar_id
 * @property string $access_token
 * @property string $access_token_expiration
 * @property string $refresh_token
 * @property string $role
 */
trait HasNorthstarToken
{
    /**
     * Make sure that token information is never included in
     * array or JSON responses by mistake.
     *
     * @return array
     */
    public function getHidden()
    {
        return array_merge($this->hidden, ['access_token', 'refresh_token', 'access_token_expiration']);
    }

    /**
     * Get the Northstar ID for the user.
     *
     * @return string|null
     */
    public function getNorthstarIdentifier()
    {
        return $this->northstar_id;
    }

    /**
     * Save the Northstar ID for the user.
     *
     * @return void
     */
    public function setNorthstarIdentifier($id)
    {
        $this->northstar_id = $id;
    }

    /**
     * Get the access token for the user.
     *
     * @return AccessToken|null
     */
    public function getOAuthToken()
    {
        // If any of the required fields are empty, return null.
        $hasAnEmptyField = empty($this->northstar_id) || empty($this->access_token) ||
            empty($this->access_token_expiration) || empty($this->refresh_token) || empty($this->role);

        if ($hasAnEmptyField) {
            return null;
        }

        return new AccessToken([
            'resource_owner_id' => $this->getNorthstarIdentifier(),
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expires' => $this->access_token_expiration,
            'role' => $this->role,
        ]);
    }

    /**
     * Save the access token for the user.
     *
     * @param AccessToken $token
     */
    public function setOAuthToken(AccessToken $token)
    {
        $this->access_token = $token->getToken();
        $this->access_token_expiration = $token->getExpires();
        $this->refresh_token = $token->getRefreshToken();
        $this->role = $token->getValues()['role'];
    }

    /**
     * Clear the access token for the user.
     *
     * @return void
     */
    public function clearOAuthToken()
    {
        $this->access_token = null;
        $this->access_token_expiration = null;
        $this->refresh_token = null;
    }
}
