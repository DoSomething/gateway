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
        $transactionId = $this->getTransactionBridge()->getHeader('X-Request-ID');

        // If there is no 'X-Request-ID' in the header, create one.
        if (! $transactionId) {
            $step = 0;
            $transactionIdHeader = ['X-Request-ID' => microtime(true) . '-' . $step];
        } else {
            // Else, if there is a 'X-Request-ID' in the header, get transaction ID and increment the step at the end of Transaction ID.
            $transactionIdBase = substr($transactionId, 0, -1);
            $step = substr($transactionId, -1) + 1;
            $transactionIdHeader = ['X-Request-ID' => $transactionIdBase . $step];
        }

        // Add to header.
        $options['headers'] = array_merge($options['headers'], $transactionIdHeader);

        $this->getTransactionBridge()->log('Request made.', ['method' => $method, 'Transaction ID' => $options['headers']['X-Request-ID'], 'Path' => $this->getUrl() . $path]);
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
