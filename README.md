# Northstar PHP [![Packagist](https://img.shields.io/packagist/v/dosomething/northstar.svg)](https://packagist.org/packages/dosomething/northstar)
This is a simple PHP API client for [Northstar](https://www.github.com/dosomething/northstar), the DoSomething.org user API.

It also includes [built-in support for Laravel 5](https://github.com/DoSomething/northstar-php#laravel-usage) and an optional [authentication driver](#laravel-authentication).

### Installation
Install with Composer:
```json
"require": {
    "dosomething/northstar": "0.1.*"
}
```

### Usage
In vanilla PHP, simply require the `Client` class and create a new instance with your API key.
```php
use DoSomething\Northstar\NorthstarClient;

$northstar = new NorthstarClient([
    'url' => 'https://northstar.dosomething.org', // the environment you want to connect to
    'api_key' => getenv('NORTHSTAR_API_KEY')      // your app's API key
]);

// And go!
$northstar->getAllUsers();
$northstar->getUser('email', 'test@dosomething.org');

// and so on...
```

### Laravel Usage
Laravel support is built-in. First, add a service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    DoSomething\Northstar\NorthstarServiceProvider::class,
]
```

Then, set your environment & key in `config/services.php`:

```php
'northstar' => [
    'url' => 'https://northstar.dosomething.org', // the environment you want to connect to
    'api_key' => env('NORTHSTAR_API_KEY')         // your app's API key
]
```

You can now resolve the API client from the service container:
```php
class Inspire
{
    protected $northstar;
    
    public function __construct(NorthstarClient $northstar)
    {
        $this->northstar = $northstar;
    }
    
    public function doSomething()
    {
        $users = $this->northstar->getAllUsers();
    }
}
```

### Laravel Authentication
A Laravel user provider is also included to configure Laravel's built-in authentication to validate against Northstar
instead of your local database. After configuring the API above, register the included user provider in the `boot`
method of your `AuthServiceProvider`:

```php
// For Laravel 5.0 or 5.1
$this->app['auth']->extend('northstar', function ($app) {
    return new \DoSomething\Northstar\Auth\NorthstarUserProvider(
        $app['northstar.auth'], $app['hash'], config('auth.model')
    );
});

// For Laravel 5.2+
$this->app['auth']->provide('northstar', function ($app, array $config) {
    return new \DoSomething\Northstar\Auth\NorthstarUserProvider(
        $app['northstar.auth'], $app['hash'], $config['auth.model']
    );
});
```

Then set your application to use the `northstar` driver instead of `eloquent` in `config/auth.php`.

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

Now, Laravel will query Northstar with user credentials, rather than your local Eloquent database. If
a matching Northstar account is found, a new instance of the specified Eloquent model will be saved to your
local database with the matching user's `northstar_id` and set as the active user for the session.

### License
&copy;2016 DoSomething.org. The Northstar PHP client is free software, and may be redistributed under the terms
specified in the [LICENSE](https://github.com/DoSomething/northstar-php/blob/master/LICENSE) file.
