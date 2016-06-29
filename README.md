# Northstar PHP [![Packagist](https://img.shields.io/packagist/v/dosomething/northstar.svg)](https://packagist.org/packages/dosomething/northstar)
This is a simple PHP API client for [Northstar](https://www.github.com/dosomething/northstar), the DoSomething.org
identity API. It supports authorization and resource requests from Northstar, and includes the tools necessary for
building other API clients that authorize against Northstar.

It also includes [built-in support for Laravel 5](https://github.com/DoSomething/northstar-php#laravel-usage) and an
optional [authentication driver](#laravel-authentication).

### Installation
Install with Composer:
```json
"require": {
    "dosomething/northstar": "^1.0.0"
}
```

### Usage
In vanilla PHP, you can require the `NorthstarClient` class and create a new instance with your credentials. You'll need
to implement your own version of the `\DoSomething\Northstar\Contracts\OAuthRepositoryContract` class to handle storing
and retrieving tokens.

```php
use DoSomething\Northstar\NorthstarClient;

$northstar = new NorthstarClient([
    'url' => 'https://northstar.dosomething.org', // the environment you want to connect to
    'client_id' => 'example', // your app's client ID
    'client_secret' => 'xxxxxxxxxxxxx', // your app's client secret
    'scope' => ['user'], // the scopes to request  
    'repository' => \YourApp\OAuthRepository::class, // class which handles saving/retrieving tokens
]);

// And go!
$northstar->getAllUsers();
$northstar->getUser('email', 'test@dosomething.org');
$northstar->updateUser('5480c950bffebc651c8b4570', ['first_name' => 'Puppet']);
$northstar->deleteUser('5480c950bffebc651c8b4570');

// and so on...

```

### Laravel Usage
Laravel support is built-in. First, add a service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    DoSomething\Northstar\Laravel\NorthstarServiceProvider::class,
],

'aliases' => [
   // ...
   'Northstar' => DoSomething\Northstar\Laravel\Facades\Northstar::class,
]
```

Then, set your environment & key in `config/services.php`:

```php
'northstar' => [
    'grant' => 'client_credentials', // OAuth grant to use: either 'password' or 'client_credentials'
    'url' => 'https://northstar.dosomething.org', // the environment you want to connect to
    'client_id' => 'example', // your app's client ID
    'client_secret' => 'xxxxxxxxxxxxx', // your app's client secret
    'scope' => ['user', 'admin'], // the scopes to request  
]
```

You can now use the `Northstar` facade anywhere in your app:
```php
class Inspire
{
    public function doSomething()
    {
        $users = Northstar::getAllUsers();
    }
}
```

### Laravel Authentication
A Laravel user provider is also included to configure Laravel's built-in authentication to validate against Northstar
instead of your local database. After configuring the client above, set your application to use the `northstar` driver
instead of `eloquent` in `config/auth.php`.

```php
// For Laravel 5.0 or 5.1
'driver' => 'northstar',
'model' => App\User::class,

// For Laravel 5.2+
'providers' => [
    'users' => [
        'driver' => 'northstar',
        'model' => App\User::class,
    ],
    // ...
 ]
```

Finally, make sure to switch the Northstar client to use the password grant in `config/services.php`:

```php
    'northstar' => [
        'grant' => 'password',
        // ...
    ]
```

Now, Laravel will query Northstar to validate user credentials, rather than your local database. If a
matching Northstar account is found, a new instance of the specified Eloquent model will be saved to your
local database with the matching user's `northstar_id` and token, and set as the active user for the session.

### License
&copy;2016 DoSomething.org. The Northstar PHP client is free software, and may be redistributed under the terms
specified in the [LICENSE](https://github.com/DoSomething/northstar-php/blob/master/LICENSE) file.
