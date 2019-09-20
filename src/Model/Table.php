<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Admin;
use Baiy\Cadmin\Helper;

trait Table
{
    private static $table;

    /**
     * 获取表名
     * @return string
     */
    public static function table()
    {
        if (!static::$table) {
            static::$table = Admin::instance()->getTablePrefix().Helper::parseTableName(static::class);
        }
        return static::$table;
    }
}