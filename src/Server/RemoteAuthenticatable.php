<?php

namespace DoSomething\Gateway\Server;

use InvalidArgumentException;
use League\OAuth2\Client\Token\AccessToken;

trait RemoteAuthenticatable
{
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the OAuth token for downstream requests.
     *
     * @return AccessToken
     */
    public function getOAuthToken()
    {
        return new AccessToken([
            'resource_owner_id' => $this->getAuthIdentifier(),
            'access_token' => token()->jwt(),
            'refresh_token' => null,
            'expires' => token()->expires()->timestamp,
            'role' => token()->role(),
        ]);
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        throw new InvalidArgumentException('Cannot access password for remote OAuth user.');
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        throw new InvalidArgumentException('Cannot access remember token for remote OAuth user.');
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        throw new InvalidArgumentException('Cannot access remember token for remote OAuth user.');
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        throw new InvalidArgumentException('Cannot access remember token for remote OAuth user.');
    }
}
