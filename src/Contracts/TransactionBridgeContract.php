<?php

namespace DoSomething\Gateway\Contracts;

interface TransactionBridgeContract
{
    /**
     * Get the value of the given HTTP header.
     *
     * @return string
     */
    public function getHeader($name);

    /**
     * Write a log message.
     *
     * @return void
     */
    public function log($message, array $details);
}
