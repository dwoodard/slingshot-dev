<?php

namespace Dwoodard\Slingshot\Facades;

use Illuminate\Support\Facades\Facade;

class Slingshot extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'slingshot';
    }
}
