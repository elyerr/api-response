<?php

namespace Elyerr\ApiExtend;

use Elyerr\ApiExtend\Controllers\TokensController;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as Provider;

final class ApiExtendServiceProvider extends Provider implements DeferrableProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->commands([
            Console\InstallCommand::class,
        ]);       
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Console\InstallCommand::class];
    }
 
}
