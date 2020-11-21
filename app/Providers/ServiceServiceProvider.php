<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Services\Api\V1\Auth\AuthService;
use App\Services\Api\V1\Post\PostService;
use App\Services\Api\V1\Auth\IAuthService;
use App\Services\Api\V1\Post\IPostService;



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
        $this->app->bind(IPostService::class, PostService::class);
        /**
         * End Version 1 Services
         */

    }
}
