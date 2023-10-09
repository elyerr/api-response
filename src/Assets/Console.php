<?php

namespace Elyerr\ApiExtend\Assets;

use Illuminate\Filesystem\Filesystem;

trait Console
{
    /**
     * instancia de Illuminate\Filesystem\Filesystem
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function fileSystem()
    {
        return new Filesystem();
    }

    /**
     * registra el controlador principal
     * @return void
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
     * Añade rutas
     *
     * @param Array  $routes
     * @param String $file
     * @param Array $imports
     * @return void
     */
    protected function addRoutes(array $routes, $file, array $imports)
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
}
