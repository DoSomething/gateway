<?php

namespace DoSomething\Gateway\Server;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class GatewayGuard implements Guard
{
    /**
     * The currently authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $user;

    /**
     * The validated JWT token.
     *
     * @var Token
     */
    protected $token;

    /**
     * Create a new authentication guard.
     *
     * @param  Token  $token
     * @param  UserProvider  $provider
     * @param  Request  $request
     */
    public function __construct(Token $token, UserProvider $provider, Request $request)
    {
        $this->token = $token;
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        // If a user ID was provided in the JWT, then
        // we should attempt to fetch their profile.
        if (! empty($this->id())) {
            $user = $this->provider->retrieveById($this->id());
        }

        return $this->user = $user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        throw new InvalidArgumentException('You can\'t verify credentials when using Gateway.');
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function authenticate()
    {
        if (! is_null($user = $this->user())) {
            return $user;
        }

        throw new AuthenticationException($this);
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        return $this->token->id;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return ! is_null($this->id());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return ! $this->check();
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return $this
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }
}
