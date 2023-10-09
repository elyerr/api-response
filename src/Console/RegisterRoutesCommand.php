<?php

namespace Elyerr\ApiExtend\Console;

use Elyerr\ApiExtend\Assets\Asset;
use Elyerr\ApiExtend\Assets\Console;
use Illuminate\Console\Command;

final class RegisterRoutesCommand extends Command
{
    use Asset, Console;

    protected $signature = "api-extend:auth";

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
            "Route::delete('tokens', [TokensController::class, 'destroyAllTokens'])",
            "Route::resource('tokens', TokensController::class)->only('index', 'store', 'destroy')",
        ],
            'routes/api.php',
            [
                "App\Http\Controllers\Auth\AuthorizationController",
                "App\Http\Controllers\Auth\TokensController",
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

        if (!file_exists(base_path("$targetPathAuth/TokensController.php"))) {
            copy(
                "$sourcePathAuth/TokensController.php",
                base_path("$targetPathAuth/TokensController.php")
            );
        }

    }

}
