## Api-Extend
Extención para API bajo el framework de Laravel. Esta extención funciona usando **Sanctum** 

## Instalar 
`composer require elyerr/api-extend`

## Agregar Controladores
`php artisan api-extend:install`

## Braadcasting 
para agregar funcionalidad de authenticacion del broadcasting atraves de tokens debemos agregar el middleware en el kernel
``` 
protected $middlewareAliases = [
    //agregar al final de las rutas
    'auth.broadcast' => \Elyerr\ApiExtend\Middleware\AuthenticateBroadcast::class,
];
```
Dentro del **BroadcastServiceProvider**, en la funcion **boot**, modificamos la siguiente linea.
```
Broadcast::routes(); 
```
La dejamos de la siguiente forma ahora el broadcast estara lista para autenticar usando cookies y tokens.
```
Broadcast::routes(['middleware' => ['auth.broadcast', 'web']]);
```

Configuracion de laravel Echo para autenticacion con token
```
import Echo from "laravel-echo";
import io from "socket.io-client"; 

window.io = io;

const options = {
  broadcaster: "socket.io",
  host: import.meta.env.VITE_APP_LARAVEL_ECHO,
  transports: ["websocket", "polling", "flashsocket"],
  auth: {
    headers: {
      Authorization: Token,
    },
  },
};

export const $echo = new Echo(options) 
```

Configuracion de laravel Echo para autenticacion por Cookies
```
import Echo from "laravel-echo";
import io from "socket.io-client"; 

window.io = io;

const options = {
  broadcaster: "socket.io",
  host: import.meta.env.VITE_APP_LARAVEL_ECHO,
  transports: ["websocket", "polling", "flashsocket"],
};

export const $echo = new Echo(options) 
```

### Tranformar recursos
Para transformar datos necesitamos registrar el middleware que se encargar de interceptar las peticiones a traves del request, este middleware require de [Laravel Fractal](https://github.com/spatie/laravel-fractal).
```
protected $middlewareAliases = [
    //agregar al final de las rutas
    'transform.request' => \Elyerr\ApiExtend\Middleware\TransformRequest::class,
];
```

Estructura de los transformadores

```
class ExampleTransformer extends TransformerAbstract
{
    use Asset;
    
    public function transform($data)
    {
        return [
            "new_key" => $data->id,           
        ];
    }
    
    public static function transformRequest($index)
    {
        $attribute = [
            "new" => 'model_key', 
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $attribute = [
            'model_key' => 'new_key', 
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attribute = [
            'new_key' => 'model_key', 
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    } 
}

```
Dentro del modelo debemos agregar una propiedad con el transformador que permita aqceder desde cualquier parte

``` 
public $transformer = AccountingTransformer::class;
```

Dentro del controlador se puede utilizar de la siguiente forma para transformar el metodo **store** y **update**
```
 public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.request:' . ExampleTransformer::class)->only('store', 'update');
    }
```
Para transformar los demas datos usando la propiedad en lugar de acceder a la clase del trasnformador

```
public function index(Accounting $accounting)
    {
        $params = $this->filter_transform($accounting->transformer);

        $data = $this->search($accounting->table, $params);

        return $this->showAll($data, $accounting->transformer, 201);
    }
```
 
### Rutas
rutas para administrar tokens y sesion de usuarios 
```
<?php

use Illuminate\Support\Facades\Route;  
use App\Http\Controllers\Auth\TokensController;
use App\Http\Controllers\Auth\AuthorizationController; 

Route::post('login', [AuthorizationController::class, 'store']);
Route::post('logout', [AuthorizationController::class, 'destroy']);
 
Route::delete('tokens', [TokensController::class, 'destroyAllTokens']);
Route::resource('tokens', TokensController::class)->only('index', 'store', 'destroy');

```
