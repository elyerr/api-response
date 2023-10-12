## Cambios realizados main
- restructurando Command Class
- creando un trait `Console` en Assets para funciones globales.
- Agregando nueva clase Command `RegisterRoutesCommand` 
- Agregando nueva clase Command `RegisterRoutesCommand` 
- correccion de sobrescribir controlador si existe
- correccion de sobrescribir Master y Auth model si existen
- Comando Modificado `api-extend:install` Instala los necesario para desarrollar una api
- nuevo comando `api-extend:auth` instala solo controladores de authenticacion
- Agregando nueva funcion `fileToArray` en el trait Asset
- creando nuevo metodo `registerPersonalAccessTokenFunction` para agregar nueva funcion `registerPersonalAccessTokenFunction` en el modelo **Auth**
- Modificando metodo `userID` en el Middlware `AuthenticateBroadcast` para que devuelva un objetoo
- Corrigiendo `user_can_join` en el Middlware `AuthenticateBroadcast`  para que permita authenticacion de multiples clases.
- Agregando nueva funcion en el trait **Asset** para generar una contraseña aleatoria
- agregando nueva funcion para contar dimesiones de arrays en el trait **Asset**
- corrigiendo condicional del middleware `TransformRequest`

## Cambios realizados v1.0.4 2023/10/07
- Solucionando conflictos con librerias existentes en el framework de laravel
- Agregando librerias al entorno en desarrollo
- Actualizando GlobalController en  `stubs\app\Http\Controllers`
- agregando pruebas unitarias a Asset Trait
- cambiando a publica la funcion `addString` en el trait Asset
- Agregando `Timestamps, HasFactory` en Master Model.

## Cambios realizados v1.0.3 2023/10/07 
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