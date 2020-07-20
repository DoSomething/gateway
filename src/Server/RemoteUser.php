<?php

namespace DoSomething\Gateway\Server;

use DoSomething\Gateway\Common\HasAttributes;
use Illuminate\Contracts\Auth\Authenticatable;

class RemoteUser implements Authenticatable
{
    use HasAttributes, RemoteAuthenticatable;

    /**
     * The user ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Have we loaded the full profile?
     *
     * @var array
     */
    protected $loaded = false;

    /**
     * Create a new RemoteUser.
     *
     * @param Token $token
     */
    public function __construct($id)
    {
        $this->id = $id;

        // If there's a token for this user attached to the request, we can
        // can use it to infer some details without pinging the OAuth server.
        if (token()->exists() && token()->id() === $id) {
            $this->token = token();
        }
    }

    /**
     * Is this attribute specified on the user?
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        // We read some fields directly from the OAuth token, and so
        // they can always be considered "set" (even if they're null):
        if (in_array($key, ['id', 'northstar_id', 'role'])) {
            return true;
        }

        // Otherwise, we'll need to load the user's full user profile
        // attributes & check if requested key is set there:
        if (! $this->loaded) {
            $this->loadAttributes();
        }

        return isset($this->attributes[$key]);
    }

    /**
     * Get an attribute, either from the token on the request or by
     * lazy-loading the full profile from the authorization server.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        // Does the token include this info? If so, just use that.
        if (in_array($key, ['id', 'northstar_id'])) {
            return $this->token->id;
        } elseif ($key === 'role') {
            return $this->token->role;
        }

        // If not, load and cache the full user profile for the
        // duration of this request. (This instance is kept by the
        // user provider.)
        if (! $this->loaded) {
            $this->loadAttributes();
        }

        if (array_key_exists($key, $this->attributes) || $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }

        return null;
    }

    /**
     * Load the attributes on this 'RemoteUser' by requesting
     * the corresponding user profile in Northstar.
     *
     * @return void
     */
    private function loadAttributes()
    {
        $user = gateway('northstar')->withToken($this->token)->getUser($this->id);

        $this->attributes = $user->toArray();
        $this->loaded = true;
    }
}
