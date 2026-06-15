<?php

namespace Elyerr\ApiResponse\Assets;

/**
 *
 */
trait Asset
{

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
}
