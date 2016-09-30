<?php

namespace DoSomething\Northstar\Laravel;

use DoSomething\Northstar\Contracts\NorthstarUserContract;
use DoSomething\Northstar\Contracts\OAuthRepositoryContract;
use League\OAuth2\Client\Token\AccessToken;
use InvalidArgumentException;

class LaravelOAuthRepository implements OAuthRepositoryContract
{
    /**
     * The User model class.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * LaravelOAuthRepository constructor.
     */
    public function __construct()
    {
        $this->model = config('auth.model');
    }

    /**
     * Get the the logged-in user.
     *
     * @return NorthstarUserContract|null
     */
    public function getCurrentUser()
    {
        $user = auth()->user();

        if (! $user instanceof NorthstarUserContract) {
            throw new InvalidArgumentException('The user model must use the HasNorthstarToken trait & the NorthstarUserContract interface.');
        }

        return $user;
    }

    /**
     * Get a user by their Northstar ID.
     *
     * @return NorthstarUserContract|null
     */
    public function getUser($id)
    {
        /** @var NorthstarUserContract $user */
        $user = $this->createModel()->where('northstar_id', $id)->first();

        if (! $user instanceof NorthstarUserContract) {
            throw new InvalidArgumentException('The user model must use the HasNorthstarToken trait & the NorthstarUserContract interface.');
        }

        return $user;
    }

    /**
     * Get the given authenticated user's access token.
     *
     * @param NorthstarUserContract $user
     *
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public function getUserToken(NorthstarUserContract $user)
    {
        return $user->getOAuthToken();
    }

    /**
     * Save the access & refresh tokens for an authorized user.
     *
     * @param \League\OAuth2\Client\Token\AccessToken $token
     * @return void
     */
    public function persistUserToken(AccessToken $token)
    {
        $northstarId = $token->getResourceOwnerId();

        /** @var NorthstarUserContract $user */
        $user = $this->getUser($northstarId);

        // If user hasn't tried to log in before, make them a local record.
        if (! $user) {
            $user = $this->createModel();
            $user->setNorthstarIdentifier($northstarId);
        }

        // And then update their token details.
        $user->setOAuthToken($token);

        $user->save();
    }

    /**
     * If a refresh token is invalid, request the user's credentials
     * by redirecting to the login screen.
     */
    public function requestUserCredentials()
    {
        // Log the current user out of the application.
        auth()->logout();

        // Save the intended path to redirect back after re-authenticating.
        session(['url.intended' => request()->fullUrl()]);

        // Redirect to the login page.
        abort(302, '', ['Location' => url('auth/login')]);
    }

    /**
     * Get the OAuth client's token.
     */
    public function getClientToken()
    {
        $client = app('db')->connection()->table('clients')
            ->where('client_id', config('services.northstar.client_credentials.client_id'))
            ->first();

        // If any of the required fields are empty, return null.
        if (empty($client->access_token) || empty($client->access_token_expiration)) {
            return null;
        }

        return new AccessToken([
            'access_token' => $client->access_token,
            'expires' => $client->access_token_expiration,
        ]);
    }

    /**
     * Save the access token for an authorized client.
     *
     * @param $clientId - OAuth client ID
     * @param $accessToken - Encoded OAuth access token
     * @param $expiration - Access token expiration as UNIX timestamp
     * @return void
     */
    public function persistClientToken($clientId, $accessToken, $expiration, $role)
    {
        /** @var \Illuminate\Database\Query\Builder $table */
        $table = app('db')->connection()->table('clients');

        // If the record doesn't already exist, add it.
        if (! $table->where(['client_id' => $clientId])->exists()) {
            $table->insert(['client_id' => $clientId]);
        }

        // Update record with the new access token & expiration.
        $table->where(['client_id' => $clientId])->update([
            'access_token' => $accessToken,
            'access_token_expiration' => $expiration,
        ]);
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }
}
