# OpenID Connect

You can use the `authorize` and `logout` methods on the client to let users log in using Northstar's single-sign on functionality. This can be implemented anywhere using a custom framework bridge, but it's super easy in Laravel:

First, set up the `login` and `logout` routes in your `routes.php`:

```php
<?php

// Authentication
Router::get('login', 'AuthController@getLogin');
Router::get('logout', 'AuthController@getLogout');
```

And forward those requests to the Northstar client in your authentication controller:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/users';

    /**
     * Where to redirect users after logout.
     *
     * @var string
     */
    protected $redirectAfterLogout = '/';

    /**
     * Handle a login request to the application.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getLogin(ServerRequestInterface $request, ResponseInterface $response)
    {
        return gateway('northstar')->authorize($request, $response, $this->redirectTo);
    }

    /**
     * Handle a logout request to the application.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getLogout(ResponseInterface $response)
    {
        return gateway('northstar')->logout($response, $this->redirectAfterLogout);
    }
}
```

Finally, add the Northstar contract & trait to your app's User model:

```php
<?php

namespace App\Models;

// ...
use DoSomething\Gateway\Contracts\NorthstarUserContract;
use DoSomething\Gateway\Laravel\HasNorthstarToken;

class User extends Model implements NorthstarUserContract, /* ... */
{
    use HasNorthstarToken, /* ... */;
    // ...
}
```

Now, Laravel will redirect to Northstar for user login and automatically create a new model in your local database
with the appropriate `northstar_id` and `role` columns. The user's access and refresh tokens will be stored so they
can make authorized requests to other DoSomething.org services.
