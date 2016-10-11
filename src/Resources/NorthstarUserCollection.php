<?php

namespace DoSomething\Gateway\Resources;

use DoSomething\Gateway\Common\ApiCollection;

class NorthstarUserCollection extends ApiCollection
{
    public function __construct($response)
    {
        parent::__construct($response, NorthstarUser::class);
    }
}
