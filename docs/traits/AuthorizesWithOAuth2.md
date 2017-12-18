# AuthorizesWithOAuth2

Use the `AuthorizesWithOAuth2` trait to authorize requests using either the OAuth 2 [authorization code grant]() or [client credentials grant](). This trait requires an implemetation of [`OAuthBridgeContract`](https://github.com/DoSomething/gateway/blob/master/src/Contracts/OAuthBridgeContract.php) to handle reading and storing access tokens.

## Example Usage

```php
<?php

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\AuthorizesWithApiKey;

class ExampleClient extends RestApiClient
{
    use AuthorizesWithOAuth2;

    /**
     * Create a new API client.
     *
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        // Set fields for `AuthorizesWithOAuth2` trait.
        $this->authorizationServerUri = $config['authorization_url'];
        $this->grant = $config['default_grant'];
        $this->config = $config;

        if (! empty($config['bridge'])) {
            $this->bridge = $config['bridge'];
        }

        parent::__construct($config['url'], $overrides);
    }
}
```

Now, any requests where the `$withAuthorization` argument is `true` will use the configured `default_grant` to fetch an access token. You can always switch grants using the `asClient` or `asUser` methods, or provide a JWT Token using the `withToken` method alongside the [`token` helper](../server/ResourceServer.md#usage).

```php
<?php

$example = new ExampleClient([
    'url' => 'https://rogue.dosomething.org',
    'authorization_url' => 'https://profile.dosomething.org',
    'default_grant' => 'authorization_code',
    'authorization_code' => [
        'client_id' => 'oauth-client-name-here',
        'client_secret' => 'top-secret-api-key',
    ],
    'client_credentials' => [
        'client_id' => 'machine-client-name-here',
        'client_secret' => 'top-secret-api-key',
    ],
]);

// Request will be signed with `authorization_code` (set as default):
$example->get('headers/');

// Request will be signed with `client_credentials`, for example
// if we're doing this in a cron job or queued task:
$example->asClient()->get('headers/');

// Request will be signed with a token provided in `Authorization` header:
$example->withToken(token())->get('headers/');
```
