<?php

namespace Baiy\Admin\Model;

trait TableTrait
{
    private static $table;

    /**
     * 获取表名
     * @return string
     */
    public static function table()
    {
        if (!static::$table) {
            $name = basename(str_replace('\\', '/', static::class));
            static::$table = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
        return static::$table;
    }
}