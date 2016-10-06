<?php

namespace DoSomething\Gateway\Laravel;

use DoSomething\Gateway\Contracts\TransactionBridgeContract;

class LaravelTransactionBridge implements TransactionBridgeContract
{
    /**
     * Get the value of the given HTTP header.
     *
     * @return string
     */
    public function getHeader($name)
    {
        return request()->header($name);
    }

    /**
     * Write a log message.
     *
     * @return void
     */
    public function log($message, array $details)
    {
        logger()->info($message, $details);
    }
}
