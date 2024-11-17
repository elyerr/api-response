<?php

namespace Elyerr\ApiResponse\Assets;

use Elyerr\ApiResponse\Exceptions\ReportError;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

trait JsonResponser
{
    /**
     * Return a message in json format
     * @param mixed $message message
     * @param mixed $code http status code
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function message($message, $code = 200)
    {
        return response()->json(['message' => $message], $code);
    }

    /**
     * Return data in json format 
     * @param mixed $collection 
     * @param mixed $code
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function data($collection, $code = 200)
    {
        return response()->json($collection, $code);
    }

    /**
     * Show one resource la object in json format
     * @param mixed $model Model instance
     * @param mixed $transformer  Model to transform data
     * @param mixed $code Http status code
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function showOne($model, $transformer = null, $code = 200)
    {
        if ($transformer != null && gettype($transformer) != "integer") {
            $model = fractal($model, $transformer);
        }

        return $this->data($model, $code);
    }

    /**
     * Show all data from any collection in json 
     * @param mixed $collection
     * @param mixed $transformer
     * @param mixed $code
     * @param mixed $pagination
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function showAll($collection, $transformer = null, $code = 200, $pagination = true)
    {
        $collection = $this->orderBy($collection);

        if ($pagination) {
            $collection = $this->paginate($collection);
        }

        if ($transformer != null && gettype($transformer) != "integer") {
            $collection = fractal($collection, $transformer);
        }

        return $this->data($collection, $code);
    }

    /**
     * Get the columns name form any table 
     * @param mixed $table table name
     * @return array
     */
    public function columns_name_table($table)
    {
        $columns = Schema::getColumnListing($table);
        return $columns;
    }

    /**
     * Generate a pagination to the collection
     * @param mixed $collection
     * @param mixed $per_page 
     * @return LengthAwarePaginator
     */
    public function paginate($collection, $per_page = 15)
    {
        throw_if(
            $per_page < 2,
            Exception::class,
            __(
                'The value of :per_page must be at least 2.',
                ['per_page' => $per_page]
            )
        );

        $page = LengthAwarePaginator::resolveCurrentPage();

        if (request()->has('per_page')) {
            $per_page = (int) request()->per_page;
        }

        $result = $collection->slice(($page - 1) * $per_page, $per_page)->values();

        $paginated = new LengthAwarePaginator($result, $collection->count(), $per_page, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    /**
     * Transform the all request using the Transform class for current model
     * through the method getOriginalAttributes 
     * @param mixed $transformer
     * @return array
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
     * Filter data using the column of the table 
     * @param mixed $table
     * @return array
     */
    public function filter($table)
    {
        return request()->only($this->columns_name_table($table));
    }

    /**
     * Search values 
     * @param mixed $table table name of the model
     * @param array $params params of the model
     * @param string $user_field name of field in the table
     * @param string $user_id current user id
     * @return Collection
     */
    public function search($table, array $params = null, string $user_field = null, string $user_id = null)
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
     * Order by collection using params order_by and order_type
     * @param mixed $collection
     * @return Collection
     */
    public function orderBy($collection)
    {
        $order_by = request()->only('order_by');
        $order_type = request()->only('order_type');

        if ($order_by) {
            foreach ($order_by as $key => $value) {
                if (isset($order_type['order_type']) and strtolower($order_type['order_type']) == "desc") {
                    $collection = $collection->sortByDesc($value);
                } else {
                    $collection = $collection->sortBy($value);
                }
            }

            $collection->values()->all();

            return collect($collection);

        } else {
            $sorted = $collection->sortDesc()->values()->all();
            return collect($sorted);
        }
    }
}
