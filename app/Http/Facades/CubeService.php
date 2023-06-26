<?php

namespace App\Http\Facades;

use Illuminate\Support\Facades\Facade;

class CubeService extends Facade
{
    /**
     * @see \App\Http\Services\CubeService
     *
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cubeService';
    }
}
