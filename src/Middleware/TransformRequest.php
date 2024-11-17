<?php

namespace Elyerr\ApiResponse\Middleware;

use Closure;
use Illuminate\Http\Request;
use Elyerr\ApiResponse\Assets\Asset;
use Illuminate\Validation\ValidationException;

class TransformRequest
{
    use Asset;

    /**
     * Transform request
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param mixed $transformer
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $transformer)
    {
        //transform Request
        $inputAttrs = [];

        foreach ($request->all() as $input => $value) {

            //checking dimensions 
            if (is_array($value) and $this->array_count_dimension($value) == 2) {
                //neeva variable
                $$input = [];

                foreach ($value as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {

                        $index = $transformer::transformRequest("$input.$key1.$key2");

                        if ($index) {
                            $data = str_replace('*', $key1, $index);
                            $data = explode('.', $data);

                            $$input[$data[1]][$data[2]] = $value2;
                        }
                        $inputAttrs[$input] = $$input;
                    }
                }
            } else {
                $inputAttrs[$transformer::transformRequest($input)] = $value;
            }
        }

        //replace request with original values of the DB
        $request->replace($inputAttrs);

        //response
        $response = $next($request);

        //Checking the errors
        if (isset($response->exception) && $response->exception instanceof ValidationException) {

            $transformErrors = [];

            //Get response data
            $data = $response->getData();

            //change the language 
            if (isset($data->message)) {
                $data->message = __($data->message);
            }

            //transform the errors
            foreach ($data->errors as $field => $error) {
                $transformedField = $transformer::transformResponse($field);

                //checking the array errors
                if (strpos($field, '.')) {
                    $old_key = explode('.', $field);
                    $transformedField = str_replace('*', $old_key[1], $transformedField);
                }

                //transform params
                $transformErrors[$transformedField] = str_replace($field, $transformedField, $error);
            }

            //replace output error
            $data->errors = $transformErrors;

            $response->setData($data);
        }
        return $response;
    }
}
