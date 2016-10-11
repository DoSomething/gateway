<?php

namespace DoSomething\Gateway\Resources;

use DoSomething\Gateway\Common\ApiCollection;

class NorthstarClientCollection extends ApiCollection
{
    public function __construct($response)
    {
        parent::__construct($response, NorthstarClient::class);
    }
}
