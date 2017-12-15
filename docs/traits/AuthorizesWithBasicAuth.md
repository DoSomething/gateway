# AuthorizesWithBasicAuth

Use the `AuthorizesWithBasicAuth` trait to use [HTTP basic authentication](http://guzzle.readthedocs.io/en/stable/request-options.html#auth) to authorize requests to an API.

## Example Usage

```php
<?php

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\AuthorizesWithApiKey;

class ExampleClient extends RestApiClient
{
    use AuthorizesWithBasicAuth;

    /**
     * Create a new API client.
     *
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        // Set fields for `AuthorizesWithBasicAuth` trait.
        $this->username = $config['user'];
        $this->password = $config['password'];

        parent::__construct($config['url'], $overrides);
    }
}
```

Now, any requests where the `$withAuthorization` argument is `true` will be authorized:

```php
<?php

$client = new ExampleClient([
    'url' => 'http://httpbin.org',
    'user' => 'admin',
    'password' => 'secret',
]);

$client->get('basic-auth/admin/secret, [], true);
```
