<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Services\Api\V1\Auth\AuthService;
use App\Services\Api\V1\Auth\IAuthService;



class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        /**
         * Start Version 1 Services
         */
        $this->app->bind(IAuthService::class, AuthService::class);
        /**
         * End Version 1 Services
         */

    }
}
