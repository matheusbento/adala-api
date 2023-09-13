<?php

namespace App\Providers;

use App\Http\Services\CubeService;
use App\Http\Services\DataProcessingService;
use App\Http\Services\PreProcessingService;
use App\Http\Services\SiloFileService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('siloFileService', SiloFileService::class);
        $this->app->bind('cubeService', CubeService::class);
        $this->app->bind('preProcessingService', PreProcessingService::class);
        $this->app->bind('dataProcessingService', DataProcessingService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
