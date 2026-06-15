<?php

namespace Elyerr\ApiResponse;

use Illuminate\Support\ServiceProvider as Provider;

final class ServiceProvider extends Provider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\TransformerCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands([
            Console\InstallCommand::class,
            Console\TransformerCommand::class,
        ]);
    }
}
