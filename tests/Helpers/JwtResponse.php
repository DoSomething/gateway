<?php

namespace DoSomething\GatewayTests\Helpers;

class JwtResponse extends JsonResponse
{
    /**
     * Make a new mock JWT token response.
     */
    public function __construct()
    {
        $body = [
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImUwNmJjMDhlZGE5NjRiZmQwNjYzZjc5MzgxNDhkZ
                TYzYjYzNzkxYmMxOTFhNTEwYmY4MDZlYzJhMGZiNjUzYzc1MjFhOWIzYTFmM2NjNjcyIn0.eyJpc3MiOiJodHRwOlwvXC9ub3J0aHN0Y
                XIuZGV2OjgwMDAiLCJhdWQiOiJ0cnVzdGVkLXRlc3QtY2xpZW50IiwianRpIjoiZTA2YmMwOGVkYTk2NGJmZDA2NjNmNzkzODE0OGRlN
                jNiNjM3OTFiYzE5MWE1MTBiZjgwNmVjMmEwZmI2NTNjNzUyMWE5YjNhMWYzY2M2NzIiLCJpYXQiOjE0NzYyMDEzMzEsIm5iZiI6MTQ3N
                jIwMTMzMSwiZXhwIjoxNDc2MjA0OTMxLCJzdWIiOiIiLCJyb2xlIjoiIiwic2NvcGVzIjpbXX0.aFyfvihTvumdWPgtfXxKQZYKVGJw-
                DnvUZO-XXNUgKYy8ngq0OTHMV00_VEqJVHPZVZ1FQv7sq-LcxpSpEM-VgfPsNUVuhJ8E3nX63ntTQQiv_CFBvippb1LJqVhfE7gzaKQc
                eatw5dT25nDlzRQIrUY1kz1exGHUozvkHAeJ_g',
        ];

        parent::__construct($body, 200);
    }
}
