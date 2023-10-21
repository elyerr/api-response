<?php

namespace Elyerr\ApiResponse\Console;

use Illuminate\Console\Command;
use Elyerr\ApiResponse\Assets\Asset;
use Elyerr\ApiResponse\Assets\Console;

final class RegisterRoutesCommand extends Command
{
    use Asset, Console;

    protected $signature = "api-response:auth";

    protected $description = "Registra los controladores para auth y tokens";

    public function handle()
    {
        $this->registerController();
        $this->registerAuth();
        $this->registerRoutes();

        $this->info("Authenticacion instalada...");
    }

    public function registerRoutes()
    {
        $this->addRoutes([
            "Route::post('login', [AuthorizationController::class, 'store'])",
            "Route::post('logout', [AuthorizationController::class, 'destroy'])",
        ],
            'routes/api.php',
            [
                "App\Http\Controllers\Auth\AuthorizationController",
            ]);
    }

    public function registerAuth()
    {
        $sourcePathAuth = __DIR__ . '/../../stubs/app/Http/Controllers/Auth';
        $targetPathAuth = 'app/Http/Controllers/Auth';

        if(!is_dir(base_path($targetPathAuth))){
            mkdir(base_path($targetPathAuth));
        }

        if (!file_exists(base_path("$targetPathAuth/AuthorizationController.php"))) {
            copy(
                "$sourcePathAuth/AuthorizationController.php",
                base_path("$targetPathAuth/AuthorizationController.php")
            );
        }

        

    }

}
