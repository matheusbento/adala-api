<?php

namespace App\Http\Facades;

use Illuminate\Support\Facades\Facade;

class PreProcessingService extends Facade
{
    /**
     * @see \App\Http\Services\PreProcessingService
     *
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'preProcessingService';
    }
}
