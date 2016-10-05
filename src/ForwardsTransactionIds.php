<?php

namespace DoSomething\Gateway;
use Request;

trait ForwardsTransactionIds
{
    /**
     * Run custom tasks before making a request.
     *
     * @see RestApiClient@raw
     */
    function runForwardsTransactionIdsTasks($method, &$path, &$options, &$withAuthorization)
    {

        // Get transaction ID and append microtime to end of Transaction ID.
        // TODO: prepend application name that is making the request to Transaction ID.
        $newTransactionIDHeader = ['X-Request-ID' => Request::header('X-Request-ID') . '-' . microtime(TRUE)];

        // add to header
        $options['headers'] = array_merge($this->defaultHeaders, $authorizationHeader, $newTransactionIDHeader);

        // add to laravel logs (doesn't this need to happen in the raw function though since we want to log that it has been sent?) - no this happens at the end of the handle function of SendReportbackToPhoenix.
    }
}
