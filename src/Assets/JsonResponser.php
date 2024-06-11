<?php

namespace Elyerr\ApiResponse\Assets;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

trait JsonResponser
{
    /**
     * muesta un mensaje en formato JSON
     * @param String $message
     * @param Integer $code
     * @return Json
     */
    public function message($message, $code = 200)
    {
        return response()->json(['message' => $message], $code);
    }

    /**
     * retorna un Objeto o colleccion de obejtos en formato Json en formato Json
     * @param \Illuminate\Support\Collection $collection
     * @param Integer $code
     * @return Json
     */
    public function data($collection, $code = 200)
    {
        return response()->json($collection, $code);
    }

    /**
     * muestra un objeto de un modelo en formato Json
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param $transformer
     * @param Integer $code
     * @return Json
     */
    public function showOne($model, $transformer = null, $code = 200)
    {
        //transforma el modelo
        if ($transformer != null && gettype($transformer) != "integer") {

            $model = fractal($model, $transformer);
        }

        return $this->data($model, $code);
    }

    /**
     * Muestra toda la colleccion en formato Json
     * @param \Illuminate\Support\Collection $collection
     * @param $transformer
     * @param Integer $code
     * @param Boolean $pagination
     * @return Json
     */
    public function showAll($collection, $transformer = null, $code = 200, $pagination = true)
    {
        //ordena los datos
        $collection = $this->orderBy($collection);

        //pagina los datos
        if ($pagination) {
            $collection = $this->paginate($collection);
        }

        //transforma los datos
        if ($transformer != null && gettype($transformer) != "integer") {
            $collection = fractal($collection, $transformer);
        }

        return $this->data($collection, $code);
    }

    /**
     * obtiene la claves o attributos de una clase
     * @param String $table
     * @return Array
     */
    public function collumns_name_table($table)
    {
        $columns = Schema::getColumnListing($table);
        return $columns;
    }

    /**
     * pagina la informacion de una colleccion por defecto pagina cada 15 resultado
     * @param \Illuminate\Support\Collection $collection
     * @param Integer $perPage
     * @return \Illuminate\Support\Collection
     *
     **/
    public function paginate($collection, $perPage = 15)
    {
        $rules = [
            'per_page' => 'integer|min:2',
        ];

        Validator::validate(request()->all(), $rules);

        $page = LengthAwarePaginator::resolveCurrentPage();

        if (request()->has('per_page')) {
            $perPage = (int) request()->per_page;
        }

        $result = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($result, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    /**
     * transforma los parametros ingresados a traves del request y solo devolvera
     * los parametros que pertenzccan al recurso transformados los cuales filtrara
     * @param Transformer $transformer
     * @return Array
     */
    public function filter_transform($transformer)
    {
        $params = array();
        foreach (request()->all() as $index => $value) {
            if ($transformer::getOriginalAttributes($index)) {
                $params[$transformer::getOriginalAttributes($index)] = $value;
            }
        }

        return $params;
    }

    /**
     * Obtiene solo pararametros de una tabla los cuales seran usados para filtrar
     * @param String $table
     * @return Array
     */
    public function filter($table)
    {
        return request()->only($this->collumns_name_table($table));
    }

    /**
     * realiza la busqueda de data usando LIKE, requiere del modelo y los parametros a filtrar
     * @param String $table
     * @param Array $params
     * Requeridos cuando se desea especificar el usuario de caul se desea obtener la data
     * @param String $user_field
     * @param String $user_id
     * @return Collection
     */
    public function search($table, array $params = null, String $user_field = null, String $user_id = null)
    {
        $sql = "SELECT * FROM {$table}";
        $bindings = [];

        if ($user_field && $user_id) {
            $sql .= " WHERE {$user_field} = ?";
            $bindings[] = $user_id;
        }

        if ($params) {
            foreach ($params as $key => $value) {
                if (empty($bindings)) {
                    $sql .= " WHERE {$key} LIKE ?";
                } else {
                    $sql .= " AND {$key} LIKE ?";
                }
                $bindings[] = "%{$value}%";
            }
        }

        $results = DB::select($sql, $bindings);

        return collect($results);
    }

    /**
     * ordena la informacion a partir de una colleccion
     * @param Collection $collection
     * @return Collection
     */
    public function orderBy($collection)
    {
        //obtenemos los datos para ordenar
        $order_by = request()->only('order_by');
        $order_type = request()->only('order_type');

        if ($order_by) {

            //ordemos los valores
            foreach ($order_by as $key => $value) {
                if (isset($order_type['order_type']) and strtolower($order_type['order_type']) == "desc") {
                    $collection = $collection->sortByDesc($value);
                } else {
                    $collection = $collection->sortBy($value);
                }
            }

            $collection->values()->all();

            //retornamos la collection con los datos ordenados
            return collect($collection);

        } else {
            $sorted = $collection->sortDesc()->values()->all();
            return collect($sorted);
        }
    }
}
