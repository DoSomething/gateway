<?php

namespace DoSomething\Northstar\Laravel;

use DoSomething\Northstar\Northstar;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class NorthstarUserProvider extends EloquentUserProvider implements UserProvider
{
    /**
     * The Northstar API client.
     * @var Northstar
     */
    protected $northstar;

    /**
     * Create a new Northstar user provider.
     *
     * @param  \DoSomething\Northstar\Northstar $northstar
     * @param  \Illuminate\Contracts\Hashing\Hasher $hasher
     * @param  string $model
     */
    public function __construct(Northstar $northstar, HasherContract $hasher, $model)
    {
        $this->northstar = $northstar;

        // When using this user provider, register an event to invalidate & remove
        // refresh token from the local database record on logout.
        app('events')->listen('auth.logout', function () {
            $this->northstar->invalidateCurrentRefreshToken();
        });

        parent::__construct($hasher, $model);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $token = $this->northstar->authorizeByPasswordGrant($credentials);

        if (! $token) {
            return null;
        }

        return $this->createModel()->where('northstar_id', $token->getResourceOwnerId())->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Is this weird? Yes.
        return true;
    }
}
