<?php

namespace Elyerr\ApiExtend\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{

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

        $this->installLibrary();

    }

    public function installLibrary()
    {
        $this->requireComposerPackages(['laravel/sanctum', 'spatie/laravel-fractal']);

        $sourcePathApp = __DIR__ . '/../../stubs/app';
        $targetPathApp = base_path('app');

        $sourcePathStubs = __DIR__ . '/../../stubs/stubs';
        $targetPathStubs = base_path('stubs');

        $filesystem = new Filesystem();

        $filesystem->copyDirectory($sourcePathApp, $targetPathApp);
        $filesystem->copyDirectory($sourcePathStubs, $targetPathStubs);

        $this->info('API Extend library ha sido instalada');
    }

    /**
     * Instalando dependencias mediante composer
     *
     * @param  array  $packages
     * @param  bool  $asDev
     * @return bool
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
     * @return bool
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

}
