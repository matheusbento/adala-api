<?php

namespace App\Http\Facades;

use Illuminate\Support\Facades\Facade;

class DataProcessingService extends Facade
{
    /**
     * @see \App\Http\Services\DataProcessingService
     *
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dataProcessingService';
    }
}
