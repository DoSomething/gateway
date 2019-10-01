# Northstar

Northstar is the DoSomething.org user & identity API.

```php
<?php

// Create the Northstar API client.
$northstar = gateway('northstar'); // or: new Northstar([...]);

// User index:
$northstar->getAllUsers([ /* query string */ ]);

// Get user (by ID, email, or mobile):
$northstar->getUser('5480c950bffebc651c8b4570');
$northstar->getUserByEmail('test@dosomething.org');
$northstar->getUserByMobile('1 (555) 123-4567');

// Create user:
$northstar->createUser([ /* profile fields */ ])

// Update user:
$northstar->updateUser('5480c950bffebc651c8b4570', ['first_name' => 'Puppet']);

// Delete user:
$northstar->deleteUser('5480c950bffebc651c8b4570');

// Merge user accounts:
$northstar->mergeUsers('5480c950bffebc651c8b4570', '5480c950bffebc651c8b4571', true); // <-- pretend!
$northstar->mergeUsers('5480c950bffebc651c8b4570', '5480c950bffebc651c8b4571');
```

## Authentication

Northstar requires [OAuth 2 authentication tokens](https://github.com/DoSomething/northstar/blob/dev/documentation/authentication.md) for most tasks. By default, the client will try to use the grant specified in the `default_grant` setting (either `authorization_code` or `client_credentials`). To switch to another grant temporarily, use the `asUser()` or `asClient()` methods.

If using Gateway's [server middleware](../server/ResourceServer.md), you can use the `token` helper & `withToken` method to forward along any provided authentication token to downstream services. This is handy because it allows you to keep the same verified user identity across any other internal API requests.

```php
<?php

// To switch to 'client_credentials':
$northstar->asClient()->get('v1/users');

// To switch to 'authorization_code':
$northstar->asUser()->get('v1/profile');

// To use a JWT provided with the request:
$northstar->withToken(token())->get('v1/profile');
```

## Resources

Responses are generally returned as `NorthstarUser` or `NorthstarClient` instances, which act as lightweight models. They make it easy to cast fields to native PHP objects (like `Carbon` instances for dates) and allow developers to easily attach helper methods or computed attributes.

```php
<?php

$user = $northstar->getUser('5480c950bffebc651c8b4570');

echo $user->created_at->diffForHumans(); // two years ago
```

Collections are returned as `ApiCollection` instances, which can be iterated over or paginated:

```php
<?php

$users = $this->northstar->getAllUsers();
$users->setPaginator(Paginator::class, [
    'path' => 'users',
]);

// then, to display pagination links in a view: {{ $users->links() }}

// Individual records can be iterated over...
foreach ($users as $user) {
    echo $user->first_name;
}

// ...or retrieved with brackets:
$puppet = $users[0];
```
