# AuthorizesWithApiKey

Use the `AuthorizesWithApiKey` trait to attach a header with a static API key to requests.

## Example Usage

```php
<?php

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\AuthorizesWithApiKey;

class ExampleClient extends RestApiClient
{
    use AuthorizesWithApiKey;

    /**
     * Create a new API client.
     *
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        // Set fields for `AuthorizesWithBasicAuth` trait.
        $this->apiKeyHeader = 'X-DS-Rest-Api-Key';
        $this->apiKey = $config['api_key'];

        parent::__construct($config['url'], $overrides);
    }
}
```

Now, any requests where the `$withAuthorization` argument is `true` will have the `X-DS-Rest-Api-Key` header set with the given key:

```php
<?php

$client = new ExampleClient([
    'url' => 'http://httpbin.org',
    'api_key' => 'top-secret-api-key',
]);

$client->get('headers/', [], true);
```
