# Resource Server

Gateway includes a Laravel authentication guard, user provider, and middleware for easily configuring applications to work with OAuth tokens. This allows your application to verify tokens created by [Northstar](https://github.com/dosomething/northstar) (or another OpenID Connect server).

## Installation

To start, let's configure Gateway's guard & user provider. In your `config/auth.php`:

```php
<?php

return [
    // ...

    'guards' => [
        // ...

        'api' => [
            'driver' => 'gateway',
            'provider' => 'northstar',
        ],
    ],

    'providers' => [
        'northstar' => [
            'driver' => 'gateway',
            'url' => env('NORTHSTAR_URL'),
            'key' => storage_path('keys/public.key'),
        ],
    ],
```

You can now run `php artisan gateway:key` to fetch Northstar's public key. This is used by Gateway to validate that tokens are really created by Northstar, and haven't been tampered with by a malicious user. You should run this command on deploys, or whenever you change `NORTHSTAR_URL`.

Finally, let's add some extra middleware to your `app/Http/Kernel.php`:

```php
<?php
// ...

protected $middlewareGroups = [
    'api' => [
        'guard:api',
        // ...
    ],
];


protected $routeMiddleware = [
    // ...
    'guard' => \DoSomething\Gateway\Server\Middleware\SetGuard::class,
    'user' => \DoSomething\Gateway\Server\Middleware\RequireUser::class,
    'role' => \DoSomething\Gateway\Server\Middleware\RequireRole::class,
    'scopes' => \DoSomething\Gateway\Server\Middleware\RequireScope::class,
];
```

## Usage

The routes in your `api` middleware group will now default to using JWT tokens for authentication!

Laravel's `Auth` facade (or `auth()` helper function) will both read data included in the JWT token now, so your API routes can use `Auth::check()` or `Auth::id()` to read the user's ID, just like you would from a database-backed web session. You can also use the built-in `auth` middleware, just like you would for any other request.

There are some extra things you might want to check from an API request. For this, you can use Gateway's middleware or `token()` helper to inspect the details of the current access token.

### Roles

We often want to allow staff members to have extra permissions that a normal user wouldn't, like reviewing a post. You can can easily restrict routes to certain roles with the included `role` middleware:

```
<?php

// Only allow staff or admin users to hit the 'delete' route.
$this->middleware('role:staff,admin')->only('delete');
```

Or use `token()->role()` to read the role and make custom assertions.

### Scopes

Scopes are used to restrict what an application can do with a user's token (for example, whether or not it can take advantage of a user's admin privileges). (Gateway will automatically check a token for the appropriate `role:staff` and `role:admin` when using the `role` middleware!)

```
<?php

// Require the 'user' and 'profile' scopes.
$this->middleware('scopes:user,profile);
```

Or use `token()->scopes()` to read the scopes directly from the token.

### And more!

There's a lot of other interesting information [included in an access token](https://github.com/DoSomething/northstar/blob/dev/documentation/authentication.md#access-tokens)! You can use `token()->client()` to check which client issued a token (for example, to automatically set a `source` field to the appropriate application). To read other included claims, use the `token()->getClaim()` method.

## PHPUnit & JWT Tokens

To make testing JWT-secured routes easy, Gateway includes some [test helpers](https://github.com/DoSomething/gateway/blob/master/src/Testing/WithOAuthTokens.php) that allow your test suite to create test tokens on-demand. To start, include the `WithOAuthTokens` trait in your test case:

```php
<?php

use DoSomething\Gateway\Testing\WithOAuthTokens;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithOAuthTokens;

    // ...
```

This will provide the `withAccessToken` method to your tests:

```php
<?php

class ExampleTest extends TestCase
{
    public function testExample()
    {
        $userId = '5554eac1a59dbf117e8b4567';
        $this->withAccessToken($userId)->getJson('/hello');

        // or a shortcut w/ a randomly-generated user ID:
        $this->withStandardAccessToken()->getJson('/hello');
    }

    public function testAdminExample()
        $adminId = '5575e568a59dbf3b7a8b4572';
        $this->withAccessToken($adminId, 'admin')->getJson('/admin');

        // or a shortcut w/ a randomly-generated user ID:
        $this->withAdminAccessToken()->getJson('/admin');
    }
}
```

## Local Development

When testing API requests on your local machine, you can configure [Paw's OAuth 2 dynamic value](https://paw.cloud/docs/auth/oauth2), [Postman's authorization tab](https://www.getpostman.com/docs/postman/sending_api_requests/authorization#oauth-20), or your API tool of choice to use OAuth 2 tokens. Provide the details for the relevant Northstar environment when configuring the request.
