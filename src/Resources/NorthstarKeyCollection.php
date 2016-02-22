<?php

namespace DoSomething\Northstar\Resources;

use DoSomething\Northstar\Common\APICollection;

class NorthstarKeyCollection extends APICollection
{
    public function __construct($response)
    {
        parent::__construct($response, NorthstarKey::class);
    }
}
