<?php

namespace DoSomething\Gateway;

use Ramsey\Uuid\Uuid;

trait ForwardsTransactionIds
{
    /**
     * Run custom tasks before making a request.
     *
     * @see RestApiClient@raw
     */
    public function runForwardsTransactionIdsTasks($method, &$path, &$options, &$withAuthorization)
    {
        $transactionId = isset($_SERVER['HTTP_X_REQUEST_ID']) ? $_SERVER['HTTP_X_REQUEST_ID'] : null;

        // If there is no 'X-Request-ID' in the header, create one.
        if (! $transactionId) {
            $transactionId = Uuid::uuid4()->toString();
        }

        // Attach request ID header to downstream API requests.
        $options['headers'] = array_merge($options['headers'], [
            'X-Request-ID' => $transactionId,
        ]);
    }
}
