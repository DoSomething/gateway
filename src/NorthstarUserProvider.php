<?php

namespace DoSomething\Northstar;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class NorthstarUserProvider extends EloquentUserProvider implements UserProvider
{
    /**
     * The Northstar API client.
     * @var NorthstarClient
     */
    protected $northstar;

    /**
     * Create a new Northstar user provider.
     *
     * @param  \DoSomething\Northstar\NorthstarClient $northstar
     * @param  \Illuminate\Contracts\Hashing\Hasher $hasher
     * @param  string $model
     */
    public function __construct(NorthstarClient $northstar, HasherContract $hasher, $model)
    {
        $this->northstar = $northstar;

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
        $user = $this->northstar->getUser('username', $credentials['username']);

        // If a matching user is found, find or create local user with that ID.
        if (! is_null($model = $this->createModel()->where('northstar_id', $user->id)->first())) {
            return $model;
        }

        $model = $this->createModel()->newInstance();
        $model->northstar_id = $user->id;
        $model->save();

        return $model;
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
        $response = $this->northstar->authorizeUser($credentials);

        return ! empty($response['access_token']);
    }
}
