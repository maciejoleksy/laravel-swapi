<?php

namespace App\Providers;

use App\Contracts\CacheInterface;
use App\Contracts\SwapiInterface;
use App\Helpers\Cache;
use App\Helpers\Swapi;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CacheInterface::class, Cache::class);
        $this->app->bind(SwapiInterface::class, Swapi::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
