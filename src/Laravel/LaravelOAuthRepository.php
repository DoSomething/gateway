<?php

namespace DoSomething\Northstar\Laravel;

use DoSomething\Northstar\Contracts\OAuthRepositoryContract;
use League\OAuth2\Client\Token\AccessToken;

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
     * Get the given authenticated user's access token.
     *
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public function getUserToken()
    {
        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = app('auth')->user();

        // If any of the required fields are empty, return null.
        if (empty($user->northstar_id) || empty($user->access_token) ||
            empty($user->access_token_expiration) || empty($user->refresh_token)
        ) {
            return null;
        }

        return new AccessToken([
            'resource_owner_id' => $user->northstar_id,
            'access_token' => $user->access_token,
            'refresh_token' => $user->refresh_token,
            'expires' => $user->access_token_expiration,
        ]);
    }

    /**
     * Save the access & refresh tokens for an authorized user.
     *
     * @param $userId - Northstar user ID
     * @param $accessToken - Encoded OAuth access token
     * @param $refreshToken - Encoded OAuth refresh token
     * @param $expiration - Access token expiration as UNIX timestamp
     * @return void
     */
    public function persistUserToken($userId, $accessToken, $refreshToken, $expiration)
    {
        $user = $this->createModel()->where('northstar_id', $userId)->first();

        $user->access_token = $accessToken;
        $user->access_token_expiration = $expiration;
        $user->refresh_token = $refreshToken;

        $user->save();
    }

    /**
     * If a refresh token is invalid, request the user's credentials
     * by redirecting to the login screen.
     */
    public function requestUserCredentials()
    {
        // Log the current user out of the application.
        app('auth')->logout();

        // Save the intended path to redirect back after re-authenticating.
        session(['url.intended' => request()->fullUrl()]);

        // Redirect to the login page.
        abort(302, '', ['Location' => url('auth/login')]);
    }

    /**
     * Remove the user's token information when they log out.
     */
    public function removeUserToken($userId)
    {
        $user = $this->createModel()->where('northstar_id', $userId)->first();

        $user->access_token = '';
        $user->access_token_expiration = '';
        $user->refresh_token = '';

        $user->save();
    }

    /**
     * Get the OAuth client's token.
     */
    public function getClientToken()
    {
        $client = app('db')->connection()
            ->table('clients')
            ->where('client_id', config('services.northstar.client_id'))
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
    public function persistClientToken($clientId, $accessToken, $expiration)
    {
        app('db')->connection()
            ->table('clients')
            ->insert([
                'client_id' => $clientId,
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
