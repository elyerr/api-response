<?php

namespace Elyerr\ApiResponse\Assets;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

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
            $model = fractal($model, $transformer)->toArray()['data'] ?? [];
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
    public function showAllByBuilder(Builder $builder, $transformer = null, $code = 200, $pagination = true)
    {
        $collection = [];
        $per_page = (int) request()->has('per_page') ? request()->get('per_page') : 50;

        if ($per_page > 500) {
            $per_page = 500;
        }

        if ($pagination) {
            $collection = $builder->paginate($per_page);
        } else {
            $collection = $builder->get();
        }

        if ($transformer != null && gettype($transformer) != "integer") {
            $collection = fractal($collection, $transformer);
        }

        return $this->data($collection, $code);
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

        if ($per_page > 500) {
            $per_page = 500;
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
     * Searcher values
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $params
     * @return Builder
     */
    public function searchByBuilder(Builder $query, array $params)
    {
        foreach ($params as $key => $value) {
            if (!isset($value) || trim($value) === '') {
                continue;
            }

            $query->whereRaw("LOWER({$key}) LIKE ?", ['%' . strtolower($value) . '%']);
        }

        return $query;
    }

    /**
     * Order by collection using params order_by and order_type
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed $transformer
     * @return Builder
     */
    public function orderByBuilder(Builder $builder, $transformer = null)
    {
        $order_by = request()->order_by;
        $order_type = request()->order_type ?? 'desc';

        if (!in_array(strtolower($order_type), ['asc', 'desc'])) {
            $order_type = 'asc';
        }

        if ($transformer) {
            if (method_exists($transformer, 'getOriginalAttributes') && $order_by) {
                $order_by = $transformer::getOriginalAttributes($order_by);
            }
        } else {
            $columns = $builder->getQuery()->getConnection()->getSchemaBuilder()->getColumnListing($builder->getQuery()->from);

            if (!in_array($order_by, $columns)) {
                $order_by = null;
            }
        }

        if ($order_by) {
            $builder->orderBy($order_by, $order_type);
        } else {
            $builder->orderBy('id', $order_type);
        }

        return $builder;
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
