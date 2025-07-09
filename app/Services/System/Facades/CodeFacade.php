<?php

namespace App\Services\Sysdef\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Access.
 */
class CodeFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'code';
    }
}
