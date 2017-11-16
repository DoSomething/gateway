# Base Client

The `RestApiClient` class is a convenient starter for building a Gateway API client - it provides some nice shortcuts for making requests, standardizes JSON parsing & error handling, and makes it easy to add extra features with traits.

## Basic API

```php
<?php

$client = new RestApiClient('http://httpbin.org');

// RESTful helpers:
$json = $client->get(string $path, array $queryParams = [], $withAuthorization = true);
$json = $client->post(string $path, array $payload = [], $withAuthorization = true)
$json = $client->put(string $path, array $payload = [], $withAuthorization = true)
$success = $client->delete(string $path, $withAuthorization = true)
```

It's generally helpful to define "named" API methods on your client, and cast responses as a `ApiResponse` or `ApiCollection`.

```php
<?php

class ExampleClient extends RestApiClient
{
    // By returning "collections" as an `ApiCollection` (or a class
    // that extends it), your response gets handy pagination helpers.
    public function getThings() {
        $response = $this->get('v1/things');

        return new ApiCollection($response);
    }

    // By returning items as an `ApiResponse` (or a class that extends
    // it), your response gets support for accessors & date casting.
    public function getThing($id) {
        $response = $this->get('v1/things/' . $id);

        if (is_null($response)) {
            return null;
        }

        return new ApiResponse($response['data']);
    }

}
```

## Error Handling

Errors will automatically be caught and casted to one of [Gateway's own exceptions](https://github.com/DoSomething/gateway/tree/master/src/Exceptions), which you can catch in your application.

While `RestApiClient` tries to assume sensible defaults about how an API formats its responses, you can customize it's default behavior by overriding methods on your client:

```php
<?php

class ExampleClient extends RestApiClient
{
    // You can override how 'delete' determines if a request is successful:
    public function responseSuccessful($json)
    {
        return true;
    }

    // ...or how it parses a 401 unauthorized exception:
    public function handleUnauthorizedException($endpoint, $response, $method, $path, $options)
    {
        throw new UnauthorizedException($endpoint, json_encode($response));
    }

    // ...or how it parses a 422 validation exception:
    public function handleValidationException($endpoint, $response, $method, $path, $options)
    {
        $errors = $response['error']['fields'];
        throw new ValidationException($response, $endpoint);
    }
}
```

## Traits

You can apply extra behavior to a client by attaching one of Gateway's traits:

```php
<?php

class ExampleClient extends RestApiClient
{

    // To use Northstar to authorize API requests:
    use AuthorizesWithNorthstar;

    // To attach 'X-Transaction-Id' headers to requests:
    use ForwardsTransactionIds;

    // ...
```
