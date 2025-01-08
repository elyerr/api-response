<?php

namespace Elyerr\ApiResponse\Console;

use Elyerr\ApiResponse\Assets\Asset;
use Elyerr\ApiResponse\Assets\Console;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    use Asset, Console;

    protected $signature = 'api-response:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register all dependencies';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {

        //$this->requireComposerPackages(['spatie/laravel-fractal']);

        //$this->installStubs();
        $this->models();
        $this->registerController();
        //$this->addMiddleware();
        //$this->addEnvironmentKeys();
        //$this->broadcastingActivate();
        //$this->registerChannels();

        $this->info('Api Response installed successfully');

    }

    /**
     * Install stubs or templates to use in your project
     * @return void
     */
    public function installStubs()
    {
        $sourcePathStubs = __DIR__ . '/../../stubs/stubs';
        $targetPathStubs = base_path('stubs');
        $this->fileSystem()->copyDirectory($sourcePathStubs, $targetPathStubs);
    }

    /**
     * Install default models to work in your Laravel Project
     * @return void
     */
    public function models()
    {

        $master = 'app/Models/Master.php';
        $auth = 'app/Models/Auth.php';
        $source = __DIR__ . '/../../stubs/';

        if (!file_exists(base_path($master))) {
            copy($source . $master, base_path($master));
        }

        if (!file_exists(base_path($auth))) {
            copy($source . $auth, base_path($auth));
        }
    }

    /**
     * Add middleware into the kernel.php file
     * @return void
     */
    public function addMiddleware()
    {
        $this->registerMiddleware([
            "'transform.request' => \Elyerr\ApiResponse\Middleware\TransformRequest::class",
        ], 'verified');
    }

    /**
     * Install packages into the Laravel project
     * @param array $packages
     * @param mixed $asDev
     * @return void
     */
    protected function requireComposerPackages(array $packages, $asDev = false)
    {
        $command = array_merge(['composer', 'require'], $packages, $asDev ? ['--dev'] : []);

        $process = new Process($command);
        $process->setWorkingDirectory(base_path());
        $process->start();

        foreach ($process as $type => $data) {
            if (Process::ERR === $type) {
                echo $data;
            } else {
                echo $data;
            }
        }

        $process->wait();

        if ($process->isSuccessful()) {
            echo "Publish config files\n";
            $this->publishAssets([
                '"Spatie\Fractal\FractalServiceProvider"',
            ]);

        } else {
            echo "Something is wrong " . $process->getErrorOutput() . "\n";
        }
    }

    /**
     * Publish providers 
     * @param array $providers
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
                    echo "Error: $returnCode\n";
                    echo $errorOutput;
                }
            } else {
                echo "Can not run the process.\n";
            }
        }
    }

    /**
     * Register middleware into the kernel.php file
     * @param array $middlewares
     * @param mixed $after
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
     * Add new environment keys into the file .env
     * @return void
     */
    protected function addEnvironmentKeys()
    {

        $readFile = fopen(base_path('.env'), 'r');

        if ($readFile) {
            $index = 0;
            while (!feof($readFile)) {
                $line = fgets($readFile);
                $index += 1;
                if (
                    strpos($line, 'ROADCAST_DRIVER') &&
                    strpos(file_get_contents(base_path('.env')), "HANNEL_NAME") === false
                ) {
                    $this->addString(base_path('.env'), $index, "CHANNEL_NAME='kumal'\n");
                    echo "Variable de entorno {CHANNEL_NAME} agregada al archivo .env\n";
                }

                if (
                    strpos($line, 'EDIS_HOST') &&
                    strpos(file_get_contents(base_path('.env')), "EDIS_PREFIX=") === false
                ) {
                    $this->addString(base_path('.env'), $index, "REDIS_PREFIX=''\n");
                    echo "Variable de entorno {REDIS_PREFIX} agregada al archivo .env\n";
                }
            }
            fclose($readFile);
        }
    }

    /**
     * Activate the broadcasting into the project
     * @return void
     */
    protected function broadcastingActivate()
    {
        $file = base_path('config/app.php');
        $readFile = fopen($file, 'r');

        if ($readFile) {
            $index = 0;
            while (!feof($readFile)) {
                $line = fgets($readFile);
                if (strpos($line, 'App\Providers\BroadcastServiceProvider::class')) {
                    $this->addString($file, $index, "\t\tApp\Providers\BroadcastServiceProvider::class,\n", 1);
                    print ("BroadCasting Activado\n");
                }
                $index += 1;
            }
        }
    }

    /**
     * Register default channels
     * @return void
     */
    protected function registerChannels()
    {
        $channel = base_path('routes/channels.php');
        $readChannel = fopen($channel, 'r');

        $routes = 'Broadcast::channel(env("CHANNEL_NAME") . ".{id}", function ($user, $id) {' . "\n\t" . 'return (int) $user->id === (int) $id;' . "\n});\n\n";
        $routes .= 'Broadcast::channel(env("CHANNEL_NAME"), function ($user) {' . "\n\t" . 'return (int) $user->id === (int) request()->user()->id; ' . "\n});";

        if ($readChannel) {
            $this->addString($channel, 15, $routes, 3);
            echo "Channels installed successfully\n";
        }
    }

}
