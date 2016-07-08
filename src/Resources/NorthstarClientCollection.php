<?php

namespace DoSomething\Northstar\Resources;

use DoSomething\Northstar\Common\APICollection;

class NorthstarClientCollection extends APICollection
{
    public function __construct($response)
    {
        parent::__construct($response, NorthstarClient::class);
    }
}
