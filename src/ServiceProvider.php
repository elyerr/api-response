<?php

namespace Elyerr\ApiResponse;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as Provider;

final class ServiceProvider extends Provider implements DeferrableProvider
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
            Console\RegisterRoutesCommand::class, 
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
        return [
            Console\InstallCommand::class,
            Console\RegisterRoutesCommand::class, 
        ];
    }

}
