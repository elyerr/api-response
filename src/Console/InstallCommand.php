<?php

namespace Elyerr\ApiExtend\Console;

use Elyerr\ApiExtend\Assets\Asset;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    use Asset;

    protected $signature = 'api-extend:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extencion extra para trabajar con una API bajo Laravel';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {

        $this->requireComposerPackages(['laravel/sanctum', 'spatie/laravel-fractal']);

        $this->installStubs();
        $this->addMiddleware();
        $this->addRoutes();
        $this->addEviromentKeys();
        $this->registerSanctumClass();
        $this->broadcastinActivate();
        $this->broadcastingServiceProvider();
        $this->registerChannels();

        $this->info('API Extend library ha sido instalada');

    }

    public function installStubs()
    {
        $fileSystem = new Filesystem();

        $sourcePathApp = __DIR__ . '/../../stubs/app';
        $targetPathApp = base_path('app');

        $fileSystem->copyDirectory($sourcePathApp, $targetPathApp);

        $sourcePathStubs = __DIR__ . '/../../stubs/stubs';
        $targetPathStubs = base_path('stubs');
        $fileSystem->copyDirectory($sourcePathStubs, $targetPathStubs);

    }

    public function addRoutes()
    {
        $this->registerRoutes([
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

    public function addMiddleware()
    {
        $this->registerMiddleware([
            "'auth.broadcast' => \Elyerr\ApiExtend\Middleware\AuthenticateBroadcast::class",
            "'transform.request' => \Elyerr\ApiExtend\Middleware\TransformRequest::class",
        ], 'verified');

    }

    /**
     * Instalando dependencias mediante composer
     *
     * @param  array  $packages
     * @param  bool  $asDev
     * @return void
     */
    protected function requireComposerPackages(array $packages, $asDev = false)
    {
        $command = array_merge(['composer', 'require'], $packages, $asDev ? ['--dev'] : []);

        $process = new Process($command);
        $process->setWorkingDirectory(base_path());

        $process->start();

        //mostrar proceso
        foreach ($process as $type => $data) {
            if (Process::ERR === $type) {
                echo $data;
            } else {
                echo $data;
            }
        }

        $process->wait();

        if ($process->isSuccessful()) {
            echo "Publicando configuraciones\n";
            $this->publishAssets([
                '"Laravel\Sanctum\SanctumServiceProvider"',
                '"Spatie\Fractal\FractalServiceProvider"',
            ]);

        } else {
            echo "Ha ocurrido un error en la ejecución. " . $process->getErrorOutput() . "\n";
        }
    }

    /**
     * Publicando providers.
     *
     * @param  array  $providers
     * @return void
     */
    protected function publishAssets(array $providers)
    {
        foreach ($providers as $provider) {

            $command = "php artisan vendor:publish --provider={$provider}";

            $descriptorspec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $process = proc_open($command, $descriptorspec, $pipes, base_path());

            if (is_resource($process)) {
                fclose($pipes[0]);

                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);

                $errorOutput = stream_get_contents($pipes[2]);
                fclose($pipes[2]);

                $returnCode = proc_close($process);

                if ($returnCode === 0) {
                    echo $output;
                } else {
                    echo "Error al ejecutar el comando. Código de retorno: {$returnCode}\n";
                    echo "Salida de error:\n";
                    echo $errorOutput;
                }
            } else {
                echo "No se pudo ejecutar el proceso.\n";
            }
        }
    }

    /**
     * Añade rutas
     *
     * @param Array  $routes
     * @param String $file
     * @param Array $imports
     * @return void
     */
    protected function registerRoutes(array $routes, $file, array $imports)
    {
        foreach ($imports as $import) {
            $this->addString(base_path($file), 3, "use {$import};\n");
        }

        // Lee el contenido actual del archivo
        $currentContent = file_get_contents(base_path($file));

        foreach ($routes as $route) {
            // Verifica si la ruta ya existe en el archivo
            if (strpos($currentContent, $route) === false) {
                // La ruta no existe, agrégala al archivo
                file_put_contents(base_path($file), "\n{$route};", FILE_APPEND);
            }
        }
    }

    /**
     * registra la clase personalizada de sanctum en AppServiceProvider
     */
    protected function registerSanctumClass()
    {
        $imports = ["Laravel\Sanctum\Sanctum","App\Models\Sanctum\PersonalAccessToken"];
        $register = "Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class)";
        $appServiceProvider = base_path('app/Providers/AppServiceProvider.php');
        $readFile = fopen($appServiceProvider, 'r');

        if ($readFile) {
            $index = 0;
            while (!feof($readFile)) {
                $line = fgets($readFile);
                if (strpos($line, "Illuminate")) {
                    $index += 1;
                    foreach ($imports as $import) {
                        $this->addString($appServiceProvider, ($index), "use {$import};\n");
                    }
                }

                if (strpos($line, "function boot()")) {
                    $index += 1;
                    $this->addString($appServiceProvider, ($index + 2), "\t\t\t{$register};\n", 1);
                }
                $index += 1;
            }
            fclose($readFile);
        }

        echo "PersonalAccessToken registrado\n";

    }

    /**
     * agrega los middleware al kernel
     * @param Array $middlewares
     * @param String $after
     * @return void
     */
    protected function registerMiddleware(array $middlewares, $after)
    {
        $file = base_path('app/Http/Kernel.php');
        $readFile = fopen($file, 'r');

        $index = 0;
        if ($readFile) {
            while (!feof($readFile)) {
                $index += 1;
                $line = fgets($readFile);
                if (strpos($line, $after)) {
                    foreach ($middlewares as $middleware) {
                        $this->addString($file, $index, "\t\t{$middleware},\n");
                    }
                    break;
                }
            }
            fclose($readFile);
        }
    }

    /**
     * agregar variables de entorno
     * @return void
     */
    protected function addEviromentKeys()
    {

        $readFile = fopen(base_path('.env'), 'r');

        if ($readFile) {
            $index = 0;
            while (!feof($readFile)) {
                $line = fgets($readFile);
                $index += 1;
                if (strpos($line, 'ROADCAST_DRIVER') &&
                    strpos(file_get_contents(base_path('.env')), "HANNEL_NAME") === false) {
                    $this->addString(base_path('.env'), $index, "CHANNEL_NAME='kumal'\n");
                    echo "Variable de entorno {CHANNEL_NAME} agregada al archivo .env\n";
                }

                if (strpos($line, 'EDIS_HOST') &&
                    strpos(file_get_contents(base_path('.env')), "EDIS_PREFIX=") === false) {
                    $this->addString(base_path('.env'), $index, "REDIS_PREFIX=''\n");
                    echo "Variable de entorno {REDIS_PREFIX} agregada al archivo .env\n";
                }
            }
            fclose($readFile);
        }
    }

    /**
     * activa el broadcast
     */
    protected function broadcastinActivate()
    {
        $file = base_path('config/app.php');
        $readFile = fopen($file, 'r');

        if ($readFile) {
            $index = 0;
            while (!feof($readFile)) {
                $line = fgets($readFile);
                if (strpos($line, 'App\Providers\BroadcastServiceProvider::class')) {
                    $this->addString($file, $index, "\t\tApp\Providers\BroadcastServiceProvider::class,\n", 1);
                    print("BroadCasting Activado\n");
                }
                $index += 1;
            }
        }
    }

    /**
     * agrega los midleware a la rutas
     */
    protected function broadcastingServiceProvider()
    {
        $provider = base_path('app/Providers/BroadcastServiceProvider.php');
        $readProvider = fopen($provider, 'r');

        if ($readProvider) {
            $index = 0;
            while (!feof($readProvider)) {
                $line = fgets($readProvider);
                if (strpos($line, '::routes')) {
                    $this->addString(
                        $provider,
                        $index,
                        "\t\tBroadcast::routes(['middleware' => ['auth.broadcast', 'web']]);\n",
                        1
                    );
                    break;
                }
                $index += 1;
            }
            fclose($readProvider);
        }
    }

    /**
     * registra los canales y el provider
     */
    protected function registerChannels()
    {
        $channel = base_path('routes/channels.php');
        $readChannel = fopen($channel, 'r');

        $routes = 'Broadcast::channel(env("CHANNEL_NAME") . ".{id}", function ($user, $id) {' . "\n\t" . 'return (int) $user->id === (int) $id;' . "\n});\n\n";
        $routes .= 'Broadcast::channel(env("CHANNEL_NAME"), function ($user) {' . "\n\t" . 'return (int) $user->id === (int) request()->user()->id; ' . "\n});";

        if ($readChannel) {
            $this->addString($channel, 15, $routes, 3);
            echo "Los canales han sido registrados\n";
        }
    }
}
