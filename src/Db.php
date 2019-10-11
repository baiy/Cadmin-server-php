<?php

namespace Baiy\Cadmin;

use Medoo\Medoo;

class Db extends Medoo
{
    // 重写执行方法 方便记录sql
    public function exec($query, $map = [])
    {
        $startTime = microtime(true);
        $result = parent::exec($query, $map);
        Admin::instance()->getContext()->addListenSql(
            $this->generate($query, $map),
            number_format((microtime(true) - $startTime), 6)
        );
        return $result;
    }
}