<?php namespace CoralSQL;

use CoralSQL\Builder;
use CoralSQL\Escape\Value;
use CoralSQL\Escape\Escaped;
use CoralSQL\Escape\Builded;

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
        if ($value instanceof Builder) {
            return new Builded($value);
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