<?php

namespace DoSomething\Gateway\Common;

class ApiResponse
{
    use HasAttributes;

    /**
     * Create a new API response model.
     *
     * @param $attributes
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }
}
