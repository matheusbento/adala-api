<?php

namespace App\Http\Facades;

use Illuminate\Support\Facades\Facade;

class SiloFileService extends Facade
{
    /**
     * @see \App\Http\Services\SiloFileService
     *
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'siloFileService';
    }
}
