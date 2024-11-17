<?php

namespace Elyerr\ApiResponse\Assets;

use Elyerr\ApiResponse\Assets\Asset;
use Illuminate\Filesystem\Filesystem;

trait Console
{
    use Asset;
    /**
     * Instance of Filesystem
     * @return Filesystem
     */
    public function fileSystem()
    {
        return new Filesystem();
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

    /**
     * Add routes
     * @param array $routes
     * @param string $file
     * @param array $imports
     * @return void
     */
    protected function addRoutes(array $routes, $file, array $imports)
    {
        foreach ($imports as $import) {
            $path = base_path($file);
            $route = "use $import;\n";
            $this->addString($path, 3, $route);
        }

        $currentContent = file_get_contents(base_path($file));

        foreach ($routes as $route) {
            if (strpos($currentContent, $route) === false) {
                file_put_contents(base_path($file), "\n{$route};", FILE_APPEND);
            }
        }
    }
}
