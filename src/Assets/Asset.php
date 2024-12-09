<?php

namespace Elyerr\ApiResponse\Assets;

use DateTime;
use Exception;
use DateTimeZone;
use ErrorException;
use DateInvalidTimeZoneException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/**
 *
 */
trait Asset
{
    /**
     * Generate a random string
     * @param int $len
     * @return string
     */
    public function passwordTempGenerate($len = 15)
    {

        $password = null;
        $string = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789*#!");

        for ($i = 0; $i < $len; $i++) {
            $password .= $string[random_int(0, count($string) - 1)];
        }
        return $password;
    }

    /**
     * Generate a unique random id
     * @param mixed $id
     * @param mixed $includeDate
     * @param mixed $includeLetters
     * @param mixed $numLetters
     * @return string
     */
    public function generateUniqueCode($id = null, $includeDate = true, $includeLetters = true, $numLetters = 5)
    {
        $code = isset($id) ? $id : rand(1, 9);
        $code .= "-";

        if ($includeDate) {
            $code .= strtotime(now());
        }

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
     * Check if two string are different
     * @param mixed $old_value current value on your model
     * @param mixed $new_value key to get by request
     * @param mixed $update_is_null  key to update if the new value is empty
     * @return bool
     */
    public function is_different($old_value, $new_value, $update_is_null = false)
    {
        if ($update_is_null) {
            return true;
        }
        return $new_value ? $old_value != $new_value : false;
    }

    /**
     * Format date in your current country date using a custom header (X-LOCALTIME) in js
     * can use this example  "X-LOCALTIME": Intl.DateTimeFormat().resolvedOptions().timeZone
     *
     * @param mixed $date
     * @param mixed $format default format (Y-m-d H:i:s)
     * @return string
     */
    public function format_date($date, $format = "Y-m-d H:i:s")
    {
        /**
         * is null
         */
        if (!isset($date)) {
            return null;
        }

        $date = new DateTime($date, new DateTimeZone('UTC'));

        try {
            /**
             * get the header and convert utc time in local time for the user
             */
            $localtime = request()->header('X-LOCALTIME');

            $date->setTimezone(new DateTimeZone($localtime));

        } catch (DateInvalidTimeZoneException $e) {
        } catch (Exception $e) {
        }

        return $date->format($format);

    }

    /**
     * Checking the time in two dates
     * @param mixed $in time to check
     * @param mixed $out end of time to check
     * @return bool
     */
    public function verify_time_is_between($in, $out)
    {
        return strtotime(now()) >= strtotime($in) and strtotime(now()) < strtotime($out);
    }

    /**
     * Change key in the transformer model, this work in this functions (transformRequest y transformResponse)
     * @param mixed $index
     * @return array|String|string
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
     * Add new string into a file
     * @param string $file file
     * @param int $index index to replace value
     * @param string $value value to replace
     * @param mixed $replace
     * @param bool $repeat
     * @return void
     */
    public function addString($file, $index, $value, $replace = 0, $repeat = false)
    {
        $lines = $this->fileToArray($file);

        if (!$repeat and strpos(file_get_contents($file), $value) === false) {

            array_splice($lines, $index, $replace, $value);

        } elseif ($repeat) {
            array_splice($lines, $index, $replace, $value);
        }
        file_put_contents($file, $lines);
    }

    /**
     * Transform any file in array collection
     * @param mixed $file
     * @return array
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
     * Check how many dimension has an array
     * @param mixed $array
     * @return int
     */
    public function array_count_dimension($array)
    {
        $dimension = 0;

        $count_dimension = function ($array) use (&$dimension, &$count_dimension) {
            if (is_array($array)) {
                $dimension += 1;
                foreach ($array as $value) {
                    return $count_dimension($value);
                }
            }
        };

        $count_dimension($array);

        return $dimension;
    }

    /**
    * checking method
    * @param mixed $method
    * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException
    * @return void
    */
    public function checkMethod($method)
    {
        if (request()->method() !== strtoupper($method)) {
            throw new MethodNotAllowedException(
                ["Expected method: $method"],
                "Method not allowed",
                405
            );
        }
    }
}
