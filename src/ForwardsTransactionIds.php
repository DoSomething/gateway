<?php

namespace DoSomething\Gateway;

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
            $newHeader = ['X-Request-ID' => uniqid() . '-0'];
        } else {
            // Otherwise, if there is a 'X-Request-ID' header, keep the
            // current transaction ID and increment the step by one.
            list($requestId, $step) = explode('-', $transactionId, 2);
            $newHeader = ['X-Request-ID' => $requestId. '-' . ($step + 1)];
        }

        // Add incremented header to downstream API requests.
        $options['headers'] = array_merge($options['headers'], $newHeader);

        // If we have a logger, write details to the log.
        if (! empty($this->logger)) {
            $this->logger->info('Request made.', [
                'method' => $method,
                'uri' => $this->getBaseUri() . $path,
                'transaction_id' => $options['headers']['X-Request-ID'],
            ]);
        }
    }
}
