<?php

namespace DoSomething\Gateway\Laravel;

use DoSomething\Gateway\Contracts\NorthstarUserContract;
use DoSomething\Gateway\Contracts\OAuthBridgeContract;
use League\OAuth2\Client\Token\AccessToken;
use InvalidArgumentException;

class LaravelOAuthBridge implements OAuthBridgeContract
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

        if (! empty($this->model) && ! in_array(NorthstarUserContract::class, class_implements($this->model))) {
            throw new InvalidArgumentException('The auth.user model must use the HasNorthstarToken trait & the NorthstarUserContract interface.');
        }
    }

    /**
     * Get the the logged-in user.
     *
     * @return NorthstarUserContract|null
     */
    public function getCurrentUser()
    {
        return auth()->user();
    }

    /**
     * Get a user by their Northstar ID.
     *
     * @return NorthstarUserContract|null
     */
    public function getUser($id)
    {
        return $this->createModel()->where('northstar_id', $id)->first();
    }

    /**
     * Find or create a local user with the given Northstar ID.
     *
     * @param $id
     * @return NorthstarUserContract
     */
    public function getOrCreateUser($id)
    {
        return $this->createModel()->firstOrCreate(['northstar_id' => $id]);
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
     *
     * @return void
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
     * Get a stored OAuth state token from the session.
     *
     * @return string
     */
    public function getStateToken()
    {
        return session('oauth_state');
    }

    /**
     * Save the OAuth state token to the session.
     *
     * @param $state
     * @return void
     */
    public function saveStateToken($state)
    {
        session(['oauth_state' => $state]);
    }

    /**
     * Log a user in to the application & update their locally cached role.
     *
     * @param NorthstarUserContract|\Illuminate\Contracts\Auth\Authenticatable $user
     * @param AccessToken $token
     * @return void
     */
    public function login(NorthstarUserContract $user, AccessToken $token)
    {
        // Save the user's role to their local account (for easy permission checking).
        $user->setRole($token->getValues()['role']);
        $user->save();

        auth()->login($user, true);
    }

    /**
     * Log the current user out of the application.
     *
     * @return void;
     */
    public function logout()
    {
        auth()->logout();
    }

    /**
     * Convert the given relative path to an absolute URL
     * with the framework's URL generator.
     *
     * @param $url
     * @return string
     */
    public function prepareUrl($url)
    {
        return url($url);
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
