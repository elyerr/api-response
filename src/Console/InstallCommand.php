<?php

namespace Elyerr\ApiResponse\Console;

use Elyerr\ApiResponse\Assets\Asset;
use Elyerr\ApiResponse\Assets\Console;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    use Asset;
    use Console;

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
        $this->models();
        $this->registerController();

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
     * Register controllers in the kernel file
     * @return bool
     */
    public function registerController()
    {
        $controllerPath = "app/Http/Controllers/GlobalController.php";
        $sourcePathController = __DIR__ . "/../../stubs/$controllerPath";

        if (!file_exists(base_path($controllerPath))) {
            copy(
                $sourcePathController,
                base_path($controllerPath)
            );

            return true;
        }

        return false;
    }

}
