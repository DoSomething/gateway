# Usage

Start by installing the latest release with [Composer](https://getcomposer.org):

```sh
composer require dosomething/gateway
```

Then, follow the instructions for [Laravel](#Laravel) or [vanilla PHP](#Vanilla-PHP).

### Laravel

Laravel support is built-in. First, add the service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    DoSomething\Gateway\Laravel\GatewayServiceProvider::class,
],
```

Then, configure the services you're using in `config/services.php`:

```php
'northstar' => [
    'grant' => 'client_credentials', // Default OAuth grant to use: either 'authorization_code' or 'client_credentials'
    'url' => 'https://northstar.dosomething.org', // the environment you want to connect to
    'key' => storage_path('keys/public.key'), // optional: used for Gateway server middleware

    // Then, configure client ID, client secret, and scopes per grant.
    'client_credentials' => [
        'client_id' => env('NORTHSTAR_CLIENT_ID'),
        'client_secret' => env('NORTHSTAR_CLIENT_SECRET'),
        'scope' => ['user'],
    ],
    'authorization_code' => [
        'client_id' => env('NORTHSTAR_AUTH_ID'),
        'client_secret' => env('NORTHSTAR_AUTH_SECRET'),
        'scope' => ['user'],
        'redirect_uri' => 'login',
    ],
]

'blink' => [
    'url' => 'https://blink.dosomething.org/api/', // the environment you want to connect to
    'user' => env('BLINK_USERNAME'),
    'password' => env('BLINK_PASSWORD'),
]
```

Publish the included migrations (and customize as needed) to add the required client & user database columns.

```
php artisan vendor:publish
```

You can now use the `gateway()` helper method anywhere in your app:

```php
class Inspire
{
    public function doSomething()
    {
        $users = gateway('northstar')->getAllUsers();

        $response = gateway('blink')->userSignup(['id' => 2]);

        // ...
    }
}
```

### Vanilla PHP

In vanilla PHP, you can manually create new clients with your credentials to make API requests. For clients that
authorize requests using OAuth2 tokens, you'll need to implement your own version of the `OAuthBridgeContract`
class to handle storing and retrieving tokens.

```php
use DoSomething\Gateway\Northstar;

$northstar = new Northstar([
    'grant' => 'client_credentials', // Default OAuth grant to use: either 'authorization_code' or 'client_credentials'
    'url' => 'https://northstar.dosomething.org', // the environment you want to connect to
    'bridge' => \YourApp\OAuthBridge::class, // class which handles saving/retrieving tokens

    // Then, configure client ID, client secret, and scopes per grant.
    'client_credentials' => [
        'client_id' => 'example',
        'client_secret' => 'xxxxxxxxxxxxx',
        'scope' => ['user'],
    ],
    'authorization_code' => [
        'client_id' => 'example',
        'client_secret' => 'xxxxxxxxxxxxx',
        'scope' => ['user'],
        'redirect_uri' => 'login',
    ],
]);

// And go!
$northstar->getAllUsers();
```
