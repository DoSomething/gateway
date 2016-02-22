<?php

namespace DoSomething\Northstar\Resources;

use DoSomething\Northstar\Common\APICollection;

class NorthstarUserCollection extends APICollection
{
    public function __construct($response)
    {
        parent::__construct($response, NorthstarUser::class);
    }
}
