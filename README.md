# api-response
Extensión para APIs bajo el framework de Laravel, incluye funciones que permite filtrar, buscar,ordenar, paginar datos devolviendo en formato json

#### Instalar versión stable
```
composer require elyerr/api-response
```

#### Instalar version en desarrollo
```
composer require elyerr/api-response dev-main
```

#### Installar lo necesario
```
php artisan api-response:install
```

#### Instalar controladores de autentificación 
estos controladores biene disponibles si estas haciendo uso de sanctum y si lo quieres usar con passport deberas modificarlos o prescindir de ellos.
```
php artisan api-response:auth
```
## Funcionalidad de JsonResponser
por defecto viene agregada en GlobalController, para lo cual si quieres hacer uso de ella puedes heredar de ese controlador o puedes llamar el trait en cada controlador, como mejor lo prefieras. mas acerca de los código de estado para respuestas puedes leer aquí [HTTP response status codes](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status), el codgo de las devoluciones por defecto es 200

#### Descripcion y uso de funciones
- `message(mensaje, codigo)`; devuelve la informacion en formato Json
- formas de uso
```
return $this->message('respuesta correcta')
return $this->message('recurso creado', 201)
```
- `data(colleccion, codigo)`; devuelve una colección en formato Json
- Formas de uso
```
return $this->data([
[id => 1, name => 'test 1'],
[id => 2, name => 'test 2']
], 200);
```

- `showOne(Objecto, $transformador, codigo)`; devulve un objeto en formato Json, usa un parámetro [transformador](https://github.com/spatie/laravel-fractal), puedes seguir el link para conocer acerca de [laravel fractal](https://github.com/spatie/laravel-fractal)]
- formas de uso
- Ejemplo sin transformador
```
$user = User::find(1);

return $this->showOne($user)
return $this->showOne($user, 201)

```
- Usando transformador: puedes leer mas abajo la sección de configuración de transformador
```
$user = User::find(2);
return $this->showOne($user, $user->transformer)
return $this->showOne($user, $user->transformer, 201)

```
- `showAll(colleccion, transformador, codigo)`
esta funcion permite mostrar datos de una colleccion en formato Json, esta tiene la funcionalidad de ordenar datos, paginarlos, transformarlos.
- para ordenarlos se deben pasar los parametros por url 
- order_by : recibe el nombre del campo a ordena
- order_type : recibes dos datos `desc` y `asc`, este parámetro viene siendo no obligatorios
- la paginacion se realiza de forma automática tiene valor por defecto pero también se le pueden cambiar por los que se requiera, esta valor al igual que el anterior se pasan como parámetros.
- per_page : por defecto es 15
- formas de uso sin transformador
```
$users = User::all();
return $this->showAll($users)
```
- forma de uso con transformador **Ver la sección acerca de los tranformadores**
```
$users = User::all();
return $this->showAll($users, UserTransformer::class, 200)

// usando la propiedad publica en el modelo user

return $this->showAll($users, $users->first()->transformer, 200)
```
- incorporar la funcion de busqueda en el metodo `showAll()` sin transformador
```
//debe existir en el modelo la propiedad publica `public $table = "users"`
$params = $this->filter($user->table)
$data = $this->search($user->table, $params)
return $this->showAll($data)
```
- usando transformadores
```
//debe existir en el modelo la propiedad publica `public $table = "users"`
//debe existir la propiedad transformer en el modelo. **ver mas la sección de transformadores**
$params = $this->filter_transform($user->transformer)
$data = $this->search($user->table, $params)
return $this->showAll($data, $user->transformer)
```
- `collumns_name_table(tabla)`
obtiene todas las claves de la tabla 
- formas de uso
```
$nombres = $this->collumns_name_table($user->table)
```
- `paginate(collecccion, perPage=15)`
pagina una colleccion por defecto su valor es 15 pero puede ser cambiado por el que desee
- formas de uso
```
$users = User::all();

data = $this->paginate($users, 100)
```

- `filter_transform($transformer)`
Como parametro recibe una clase Trasnformadora, devulve todos los campos una tabla transformada o enmascarada.
- formas de uso
```
$params = $this->filter_transform($user->transformer);

```

- `filter(tabla)`, igual que la anterior pero usa el nombre de la tabla
- formas de uso
```
$params = $this->filter($user->table)

```

- `search(tabla, parametros)`, esta función realiza una búsqueda usando el operador LIKE y solo acepta como parámetros los campos de la tabla del modelo que se este empleando, esta se suele usar junto con las dos funciones anteriores permitiendo enmascarar las busquedas o simplemente mostrado los campos como esta en la base de datos
- Formas de uso
```
// puede ser asi
$params = $this->filter_transform($user->transformer);
// o puede ser asi
$params = $this->filter($user->table)

//se puede usar con parametros
$data = $this->search($user->table, $params)

//o sin parametros
$data = $this->search($user->table)

```

- `orderBy($colleccion)`
esta función viene integrada en la función principal `showAll`, y los parámetros, se le pasan por url, puedes ver la sección de `showAll` para entender su funcionamiento

## Funcionalidad de Timestamps
este trait puede ser impementado en un modelo para que no use transformaderes para formatear la fecha de las fechas en el formato `AÑO-MES-DIA H:I`   por ejemplo `2023-12-01 15:30`

## Funcionalidad  de Asset

- `passwordTempGenerate(len)`; genera una cadena aleatoria, por defecto es de 15
  - formas de uso
    ```
     $this->passwordTempGenerate(20);
    ```
- `generateUniqueCode($id, $includeDate, $includeLetters, $numLetters)`; genera un codigo unico.
  - formas de uso
    ```
    this->generateUniqueCode()
    this->generateUniqueCode(11001, true, true, 10)
    
    ```
- `is_diferent($old_value, $new_value, $update_is_null)`, verifica si una cadena de texto ha cambiado
  - `old_value` : valor a verificar o valor antiguo
  - `new_value` : valor nuevo que va a reemplazar el anterior
  - `update_is_null`:  por defecto es `false`, si se cambia a true, verificara si asi sea nulo el valor
  - formas de uso
    ```
    $this->is_diferent('valor_antiguo', 'valor nuevo'); //true
    $this->is_diferent('valor_antiguo', ''); //false
    $this->is_diferent('valor_antiguo', '', true); //true

    ```
- `format_date(string)`, tranforma una fecha que puede provenir de un campo de una tabla, esta normalmente se usa en los transformadores, el formato que usa es `Y-m-d H:i:s`
  - forma de uso
    ```
    $this->format_date($user->created_at) 
    ```
- `verify_time_is_betweem(inicio, final)`, verifica si la fecha actual esta en un rango de fechas
  - Formas de uso
    ``` 
    $this->verify_time_is_betweem('2022-12-13', '2023-11-22')
    $this->verify_time_is_betweem('2022-12-13 12:15', '2023-12-22 23:45')
    ```
- `changeIndex(index)`, nomalmente se usa para transformar parametros del request en los transformadores, el middleware `transform.request:transformador`  debe ser agregado para los metodos store y update en el controlador en uso o bueno en las cunciones para crear o actualizar recursos
  - Formas de uso
    ```
    //formas de aplicacion del trasnformador en el controlador
    public function __construct(User $user){
        $this->middleware('transform.request:' . $user->transformer)
    }

    //ejemplo reglas en el request
    public function rules()
    { 
        return [
            'user' => ['required', 'array'],
            'user.*.name' => ['required','exists:table,id'],
            'user.*.last_name' => ['required','integer'],
        ];
    }

    //estas funcion debe ser implementada en el transformador
    public static function transformRequest($index)
    {   
        //forma de uso
        $index = Asset::changeIndex($index);

        $attribute = [
            'user' => 'user',
            'user.*.nombre' => 'user.*.name',
            'user.*.apellido' => 'user.*.last_name',
        ];
         
        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $index = Asset::changeIndex($index);

        $attribute = [
            'user' => 'user',
            'user.*.name' => 'user.*.nombre',
            'user.*.last_name' => 'user.*.apellido',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }
    ```

- `addString($file, $index, $value, $replace = 0, $repeat = false)`, agrega un texto a un archivo en php.
  - `file`, ruta del archivo
  - `index`, posicion de la linea en el archivo don ira el texto,
  - `value`, valor que se agregara al archivo
  - `repacle`, 1 para remplazar y 0 para no remplazar
  - `repeat`, true para agregar la linea en caso exista, y false para no agregarla si ya existe,

  - formas de uso
    ```
    $file = 'ruta';

    //
    $this->addString($file, 12, "nuevo texto", 1, false)
    ```

- `fileToArray(file)`, convierte cada linea de un archivo en array
  - formas de uso
    ```
    $file = 'ruta';
    $this->fileToArray($file);
    ```
- `array_count_dimension(array)`; cuenta las dimenciones de un array
  - formas de uso
    ```
    $array = [
        [
            [
                'id' => 1,
                'name' => 'tess'
            ]
        ]
    ]
    $this->array_count_dimension($array) //3
    ```

## Uso de los Transformadores
para crear un transformador debe usarse el comando `php artisan make:middleware UserTransformer`, puedes revisar la [documentacion oficial](https://github.com/spatie/laravel-fractal).

- Esquema de los transformadores, todas esta funciones son requeridas en todos los que se implementen
    ```
    public function transform($role)
        {
            return [
                'id' => $role->id,
                'role' => $role->name,
                'descripcion' => $role->description,
            ];
        }

        public static function transformRequest($index)
        {
            $attribute = [
                'role' => 'name',
                'descripcion' => 'description',
            ];

            return isset($attribute[$index]) ? $attribute[$index] : null;
        }

        public static function transformResponse($index)
        {
            $attribute = [
                'name' => 'role',
                'description' => 'descripcion',
            ];

            return isset($attribute[$index]) ? $attribute[$index] : null;
        }

        public static function getOriginalAttributes($index)
        {
            $attributes = [
                'id' => 'id',
                'nombre' => 'name',
                'descripcion' => 'description',
            ];

            return isset($attributes[$index]) ? $attributes[$index] : null;
        }

    ```

- Aplicacion en la clase

    ```

    class User extends Auth
    {
        
        public $table = "users";


        public $transformer = UserTransformer::class;

    }   


    ```
- Aplicacion en el controlador

    ```
    public function __construct(User $user){
        $this->middleware('transform.request:' . $user->transformer)
    }

    ```
