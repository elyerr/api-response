<?


if (!function_exists("generateRandomString")) {
    /**
     * Generate random string
     * @param mixed $len
     * @return string|null
     */
    function generateRandomString($len = 15)
    {
        $password = null;
        $string = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789*#!/?¡$");

        for ($i = 0; $i < $len; $i++) {
            $password .= $string[random_int(0, count($string) - 1)];
        }
        return $password;
    }
}


if (!function_exists("generateUniqueCode")) {
    /**
     * Generate unique code
     * @param mixed $id
     * @param mixed $includeDate
     * @param mixed $includeLetters
     * @param mixed $numLetters
     * @return string
     */
    function generateUniqueCode($id = null, $includeDate = true, $includeLetters = true, $numLetters = 5)
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
}


if (!function_exists('format_date')) {
    /**
     * Format date in your current country date using a custom header (X-LOCALTIME) in js
     * can use this example  "X-LOCALTIME": Intl.DateTimeFormat().resolvedOptions().timeZone
     *
     * @param mixed $date
     * @param mixed $format default format (Y-m-d H:i:s)
     * @return string
     */
    function format_date($date, $format = "Y-m-d H:i:s")
    {
        /**
         * is null
         */
        if (!isset($date)) {
            return null;
        }

        $date = new \DateTime($date, new \DateTimeZone('UTC'));

        try {
            /**
             * get the header and convert utc time in local time for the user
             */
            $localtime = request()->header('X-LOCALTIME');

            $date->setTimezone(new \DateTimeZone($localtime));

        } catch (\DateInvalidTimeZoneException $e) {
        } catch (\Exception $e) {
        }

        return $date->format($format);
    }
}

if (!function_exists('format_money')) {
    /**
     * Format money
     * @param mixed $date
     * @param mixed $decimal_separator
     * @param mixed $thousands
     * @return string
     */
    function format_money($date, $decimal_separator = ".", $thousands = ",")
    {
        return number_format($date / 100, 2, $decimal_separator, $thousands);
    }
}


if (!function_exists('verify_time_is_between')) {
    /**
     * Checking the time in two dates
     * @param mixed $in time to check
     * @param mixed $out end of time to check
     * @return bool
     */
    function verify_time_is_between($in, $out)
    {
        return strtotime(now()) >= strtotime($in) and strtotime(now()) < strtotime($out);
    }
}

if (!function_exists('transformRequest')) {
    function transformRequest(array $data, string $prefix = '')
    {
        $flattened = [];

        foreach ($data as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $flattened += transformRequest($value, $newKey);
            } else {
                $flattened[$newKey] = $value;
            }
        }

        return $flattened;
    }
}


if (!function_exists('array_count_dimension')) {
    /**
     * Check how many dimension has an array
     * @param mixed $array
     * @return int
     */
    function array_count_dimension($array)
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
}