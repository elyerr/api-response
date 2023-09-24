<?php

namespace Elyerr\ApiExtend\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

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
        $sourcePathApp = __DIR__ . '/../../stubs/app';
        $targetPathApp = base_path('app');

        $sourcePathStubs = __DIR__ . '/../../stubs/stubs';
        $targetPathStubs = base_path('stubs');

        $filesystem = new Filesystem;

        $filesystem->copyDirectory($sourcePathApp, $targetPathApp);
        $filesystem->copyDirectory($sourcePathStubs, $targetPathStubs);

        $this->info('API Extend library ha sido instalada');
    }

}
