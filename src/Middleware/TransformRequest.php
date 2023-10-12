<?php

namespace Elyerr\ApiExtend\Middleware;

use Closure;
use Illuminate\Http\Request;
use Elyerr\ApiExtend\Assets\Asset;
use Illuminate\Validation\ValidationException;

class TransformRequest
{
    use Asset;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param $transformer
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $transformer)
    {
        //transform Request
        $inputAttrs = [];

        foreach ($request->request->all() as $input => $value) {

            //evaluamos si es un array y que sea de segunda dimencion
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
        //reemplazamo el request con los datos originales de la BD
        $request->replace($inputAttrs);

        //response
        $response = $next($request);
        
        //evaluamos si existen errores
        if (isset($response->exception) && $response->exception instanceof ValidationException) {
            
            $transformErrors = [];

            //se obtinen los datos del response
            $data = $response->getData();

            //cambiando lenguaje del mensaje
            if (isset($data->message)) {                 
                $data->message = __($data->message);
            }

            //recorremos todos los erros
            foreach ($data->errors as $field => $error) {
                
                //por cada parametro , transformamos los datos originales
                $transformedField = $transformer::transformResponse($field);

                /**
                 * verificamos que los datos transformados sean arrays 
                 */
                if (strpos($field, '.')) {
                    $old_key = explode('.', $field);
                    $transformedField = str_replace('*', $old_key[1], $transformedField);
                }
                //transformamos los parametros 
                $transformErrors[$transformedField] = str_replace($field, $transformedField, $error);
            }

            //reeplazamos los errores originales con los de salida
            $data->errors = $transformErrors;
            
            $response->setData($data);
        }
        return $response;
    }
}
