<?php

namespace DoSomething\Gateway\Resources;

use DoSomething\Gateway\Common\APICollection;

class NorthstarClientCollection extends APICollection
{
    public function __construct($response)
    {
        parent::__construct($response, NorthstarClient::class);
    }
}
