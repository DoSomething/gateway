<?php

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Northstar;

if (! function_exists('gateway')) {
    /**
     * Return a registered Gateway client from the Laravel service container.
     *
     * @param string $client
     * @return Northstar|RestApiClient
     */
    function gateway($client)
    {
        if ($client !== 'northstar') {
            throw new InvalidArgumentException('There isn\'t a Gateway client registered with that name.');
        }

        return app(Northstar::class);
    }
}
