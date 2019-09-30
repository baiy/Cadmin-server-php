<?php

namespace Baiy\Cadmin;

class Helper
{
    public static function parseTableName($class)
    {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", basename(str_replace('\\', '/', $class))), "_"));
    }
}