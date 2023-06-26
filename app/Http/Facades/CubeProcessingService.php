<?php

namespace App\Http\Facades;

use Illuminate\Support\Facades\Facade;

class CubeProcessingService extends Facade
{
    /**
     * @see \App\Http\Services\CubeProcessingService
     *
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cubeProcessingService';
    }
}
