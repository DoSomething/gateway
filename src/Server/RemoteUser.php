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
            $user = gateway('northstar')->withToken($this->token)->getUser('id', $this->id);

            $this->attributes = $user->toArray();
            $this->loaded = true;
        }

        if (array_key_exists($key, $this->attributes) || $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }

        return null;
    }
}
