<?php

namespace DoSomething\Gateway\Resources;

use Illuminate\Contracts\Auth\Authenticatable;
use Prophecy\Exception\InvalidArgumentException;

class AuthenticatableUser extends NorthstarUser implements Authenticatable
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
        return $this->getId();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        throw new InvalidArgumentException('Cannot get password from JWT session.');
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        throw new InvalidArgumentException('Cannot use remember tokens with JWT session.');
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        throw new InvalidArgumentException('Cannot use remember tokens with JWT session.');
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        throw new InvalidArgumentException('Cannot use remember tokens with JWT session.');
    }
}
