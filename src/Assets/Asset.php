<?php

namespace Elyerr\ApiResponse\Assets;

use ErrorException;

/**
 *
 */
trait Asset
{

    /**
     * Set the temporal password.
     *
     * @param  paramType  $value
     * @return void
     */
    public function passwordTempGenerate($len = 15)
    {

        $string =  str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789*#!");

        //nueva cadena a generar

        $password = null;

        //cantidad de letras a tomar del abc y generear unnuevo string

        for ($i = 0; $i < $len; $i++) {

            $password .= $string[random_int(0, count($string) - 1)];

        }
        return $password;
    }

    /**
     * genera un codigo unico
     * @param String $id
     *
     */
    public function generateUniqueCode($id = null, $includeDate = true, $includeLetters = true, $numLetters = 5)
    {
        // Generar la parte del ID
        $code = isset($id) ? $id : rand(1, 9);
        $code .= "-";

        // Generar la parte de la fecha actual
        if ($includeDate) {
            $code .= strtotime(now());
        }

        // Generar la parte de letras aleatorias
        if ($includeLetters) {
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $lettersLength = strlen($letters);

            for ($i = 0; $i < $numLetters; $i++) {
                $code .= $letters[rand(0, $lettersLength - 1)];
            }
        }

        return $code;
    }

    /**
     * verifica si dos valores son diferentes y recibe 3 parametros
     * el ultimo parametro es opcional se utiliza cuando quieres que
     * actualize valores nulos
     * @param mixed $old_value
     * @param mixed $new_value
     * @param boolean $update_is_null
     * @return boolean
     */
    public function is_diferent($old_value, $new_value, $update_is_null = false)
    {
        if ($update_is_null) {
            return true;
        }
        return $new_value ? strtolower($old_value) != strtolower($new_value) : false;
    }

    /**
     * formatea una fecha con el formato Y-m-d H:i:s
     * @param String $date
     * @return DateTime
     */
    public function format_date($date)
    {
        return isset($date) ? date('Y-m-d H:i:s', strtotime($date)) : null;
    }

    /**
     * Verifica que dos fechas esten en el rango
     * @param String $in
     * @param String $out
     * @return boolean
     */
    public function verify_time_is_betweem($in, $out)
    {
        return strtotime(now()) >= strtotime($in) and strtotime(now()) < strtotime($out);
    }

    /**
     * funciona para transformar parametros detro de las funciones trasnformRequest y transformResponse
     * como parametro recibe el index
     * @param String $index
     * @return String
     */
    public static function changeIndex($index)
    {
        try {
            $number = explode(".", $index)[1];
            return str_replace($number, '*', $index);
        } catch (ErrorException $e) {
            return $index;
        }
    }

    /**
     * añade un nuevo texto en una nueva linea dentro del archivo
     * @param String $file
     * @param Int $index
     * @param String $value
     * @param Boolean $repeat
     * @return void
     */
    public function addString($file, $index, $value, $replace = 0, $repeat = false)
    {
        $lines = $this->fileToArray($file);

        //comprovamos que el valor no exista
        if (!$repeat and strpos(file_get_contents($file), $value) === false) {
            //agregamo el nuevo valor al indice que queremos
            array_splice($lines, $index, $replace, $value);

        } elseif ($repeat) { //si no son necesarios valores repetidos
            //agregamo el nuevo valor al indice que queremos
            array_splice($lines, $index, $replace, $value);
        }
        //reemplazar los datos del archivo original
        //con los datos del array
        file_put_contents($file, $lines);
    }

    /**
     * convierte un archivo en array
     * @param String $file
     * @return Array
     */
    public function fileToArray($file)
    {
        $readFile = fopen($file, 'r');

        $lines = [];

        if ($readFile) {
            while (!feof($readFile)) {
                $line = fgets($readFile);
                array_push($lines, $line);
            }
            fclose($readFile);
        }

        return $lines;
    }

    /**
     * cuenta cuantas dimensiones tiene un array, devolviendo un valor numerico 
     * correspondiente a la dimension, si no es un array devolvera 0
     * @param Array $array
     * @param Int
     */
    public function array_count_dimension($array)
    {   
        $dimension = 0;
        //funcion anonima
        $count_dimension = function ($array) use (&$dimension, &$count_dimension) {
            if (is_array($array)) {
                $dimension += 1;
                foreach ($array as $value) {
                    return $count_dimension($value);
                    break;
                }
            }
        };
        //ejecucion
        $count_dimension($array);

        return $dimension;
    }
}
