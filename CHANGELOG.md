## Cambios realizados main 
- agregando registro de los middleware dentro de providers automaticamente
- registrando canales en `routes/api.php` automaticamente
- agregando nombre al canal en el archivo `.env`
- corrigiendo duplicidad de variables en el archivo `.env`
- registrando `PersonalAccessToken` en `AppServiceProvider`
- eliminando `ReportError` de los `stubs`

## Cambios realizados v1.0.2 2023/10/06
- registrando Middleware `auth.broadcast`y`transform.request` de forma automatica en el `kernel.php`
- agregando variables de entorno `CHANNEL_NAME`  y `REDIS_PREFIX` al archivo `.env``
- cambiando `envets` y `listners` a `src`
- registrando rutas de forma automatica dentro de `routes/api.php`
- Instalado dependecias `laravel/sanctum` y `spatie/laravel-fractal` de forma automatica.
- Publicando assets `laravel/sanctum` y `spatie/laravel-fractal` de forma automatica.
- Agregando nueva funcion para modificar archivos en el trait `Elyerr\ApiExtend\Assets\Asset`
- Activado de forma automatica el broadcasting routes