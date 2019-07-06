<?php namespace CoralSQL;

use CoralSQL\Escape\Value;
use CoralSQL\Escape\Escaped;

class Escape
{
    /**
     * encode
     * @param string|Value $value
     * @return Value
     */
    public static function encode($value): Value
    {
        if ($value instanceof Value) {
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