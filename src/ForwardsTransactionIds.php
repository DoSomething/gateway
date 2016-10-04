<?php

namespace DoSomething\Gateway;

trait ForwardsTransactionIds
{
    /**
     * Run custom tasks before making a request.
     *
     * @see RestApiClient@raw
     */
    function runForwardsTransactionIdsTasks($method, &$path, &$options, &$withAuthorization)
    {
        // ...
    }
}
