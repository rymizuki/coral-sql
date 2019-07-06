<?php namespace CoralSQL;

use CoralSQL\Escape\Value;
use CoralSQL\Escape\Escaped;

class Escape
{
    /**
     * encode
     * @param string|CoralSQL\Escape\Value $value
     */
    public static function encode($value): ?Value
    {
        if ($value === null || $value instanceof Value) {
            return $value;
        }

        $values = explode('.', $value);
        $output = join('.', array_map(function ($name) {
            // ignore escape when ',",' started and ended
            if (preg_match("/(?:\"(?:.+?)\")|(?:'(?:.+?)')|(?:`(?:.+?)`)/", $name)) {
                return $name;
            }
            return "`${name}`";
        }, $values));

        return new Escaped($output);
    }
}