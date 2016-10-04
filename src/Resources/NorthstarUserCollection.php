<?php

namespace DoSomething\Gateway\Resources;

use DoSomething\Gateway\Common\APICollection;

class NorthstarUserCollection extends APICollection
{
    public function __construct($response)
    {
        parent::__construct($response, NorthstarUser::class);
    }
}
