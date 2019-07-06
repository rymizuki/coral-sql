<?php namespace CoralSQL;

use PDO;
use Exception;

class DataType
{
    public static function getDataType($value): int
    {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_string($value)) {
            return PDO::PARAM_STR;
        } else {
            throw new Exception("'${value}' is not defined data type");
        }
    }
}
