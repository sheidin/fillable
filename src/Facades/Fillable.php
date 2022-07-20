<?php

namespace Sheidin\Fillable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sheidin\Fillable\Fillable
 */
class Fillable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fillable';
    }
}
