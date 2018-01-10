# ForwardsTransactionIds

Use the `ForwardsTransactionIds` trait to forward any `X-Request-ID` header included on a request. This can be helpful to be able to track a request lifecycle between multiple applications.

If a `X-Request-ID` header does not exist, Gateway will generate a new one for each downstream request.

## Example Usage

```php
<?php

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\ForwardsTransactionIds;

class ExampleClient extends RestApiClient
{
    use ForwardsTransactionIds;

    /**
     * Create a new API client.
     *
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        parent::__construct($config['url'], $overrides);
    }
}
```

Now, any requests made by this client will contain a `X-Request-ID` header:

```php
<?php

$client = new ExampleClient([
    'url' => 'http://httpbin.org',
]);

// Pass a Monolog\Logger instance.
$client->setLogger($logger);

$client->post('headers/', [], true);
```
