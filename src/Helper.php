<?php

namespace Baiy\Cadmin;

class Helper
{
    public static function parseTableName($class)
    {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", basename(str_replace('\\', '/', $class))), "_"));
    }

    public static function url()
    {
        $url = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $url .= "s";
        }
        $url .= "://";
        if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
            $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $url;
    }

    public static function ip()
    {
        if (isset($_SERVER['SERVER_NAME'])) {
            return gethostbyname($_SERVER['SERVER_NAME']);
        }
        $ip = "";
        if (isset($_SERVER)) {
            if (isset($_SERVER['SERVER_ADDR'])) {
                $ip = $_SERVER['SERVER_ADDR'];
            } elseif (isset($_SERVER['LOCAL_ADDR'])) {
                $ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $ip = getenv('SERVER_ADDR');
        }
        return $ip ?: "";
    }
}