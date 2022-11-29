<?php

namespace Marshmallow\NovaFormbuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Marshmallow\NovaFormbuilder\NovaFormbuilder
 */
class NovaFormbuilder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Marshmallow\NovaFormbuilder\NovaFormbuilder::class;
    }
}
