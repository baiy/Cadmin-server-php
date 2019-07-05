<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Helper;

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
            static::$table = Helper::parseTableName(static::class);
        }
        return static::$table;
    }
}