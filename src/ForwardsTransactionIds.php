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
            $newHeader = ['X-Request-ID' => microtime(true) . '-0'];
        } else {
            // Otherwise, if there is a 'X-Request-ID' header, keep the
            // current transaction ID and increment the step by one.
            list($requestId, $step) = explode('-', $transactionId, 2);
            $newHeader = ['X-Request-ID' => $requestId. '-' . ($step + 1)];
        }

        // Add incremented header to downstream API requests.
        $options['headers'] = array_merge($options['headers'], $newHeader);

        // @TODO: Log the request.
    }

    /**
     * Get the OAuth repository used for storing & retrieving tokens.
     * @return OAuthBridgeContract $repository
     * @throws \Exception
     */
    private function getTransactionBridge()
    {
        if (! class_exists($this->transactionBridge)) {
            throw new \Exception('You must provide an implementation of TransactionBridgeContract to store tokens.');
        }

        return new $this->transactionBridge();
    }
}
