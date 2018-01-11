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
            $transactionId = uniqid();
        }

        // Attach request ID header to downstream API requests.
        $options['headers'] = array_merge($options['headers'], [
            'X-Request-ID' => $transactionId,
        ]);

        // If we have a logger, write details to the log.
        if (! empty($this->logger)) {
            $this->logger->info('Request made.', [
                'method' => $method,
                'uri' => $this->getBaseUri() . $path,
                'request_id' => $options['headers']['X-Request-ID'],
            ]);
        }
    }
}
