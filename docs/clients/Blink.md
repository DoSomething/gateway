# Blink

Gateway includes a basic API client for [DoSomething/blink](https://github.com/DoSomething/blink). Requests are authorized with HTTP Basic Authentication.

```php
// Create the Blink API client.
$blink = gateway('blink'); // or: new Blink([...]);

// Create Blink events!
$success = $blink->userSignup([
    'id' => 4036838,
    'northstar_id' => '598ca42c10707d7680749f81',
    'campaign_id' => 7,
    'campaign_run_id' => 7818,
    'quantity' => 12,
    'quantity_pending' => null,
    'why_participated' => 'I love to test!',
    'source' => 'niche',
    'created_at' => '2017-08-10 18:21:35',
    'updated_at' => '2017-08-10 18:21:35',
]);
```

For more calls and usage examples see [Blink](https://github.com/DoSomething/gateway/blob/master/src/Blink.php) and [BlinkTest](https://github.com/DoSomething/gateway/blob/master/tests/BlinkTest.php) classes.
