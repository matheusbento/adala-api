<?php

namespace App\Providers;

use App\Http\Services\PreProcessingService;
use App\Http\Services\ProcessingService;
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
        $this->app->bind('preProcessingService', PreProcessingService::class);
        $this->app->bind('processingService', ProcessingService::class);
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
