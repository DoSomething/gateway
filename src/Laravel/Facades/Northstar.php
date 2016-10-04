<?php

namespace DoSomething\Gateway\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Northstar extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'northstar';
    }
}
