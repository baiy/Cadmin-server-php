<?php

namespace Baiy\Cadmin;

class Helper
{
    public static function checkPassword($inputPassword, $hashPassword)
    {
        return password_verify($inputPassword, $hashPassword);
    }

    public static function createPassword($inputPassword)
    {
        return password_hash($inputPassword, PASSWORD_DEFAULT);
    }

    public static function parseTableName($class)
    {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", basename(str_replace('\\', '/', $class))), "_"));
    }
}