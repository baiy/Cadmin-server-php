<?php

namespace Baiy\Admin\System;

use Baiy\Admin\Handle;

class Base
{
    protected $adapter;

    public function __construct()
    {
        $this->adapter = Handle::instance()->getAdapter();
    }

    public function page($table, $where = [], $bindings = [], $order = "")
    {
        $where = $where ? "where ".implode(" and ",$where) : "";
        $order = $order ? "order by ".$order : "";

        $offset = max(0, intval($this->adapter->input('offset', 1)));
        $pageSize = max(1, min(200, intval($this->adapter->input('pageSize', 20))));

        $lists = $this->adapter->select(
            "select * from {$table} {$where} {$order} limit {$offset},{$pageSize}",
            $bindings
        );
        $count = $this->adapter->count(
            "select count(*) as `total` from {$table} {$where}",
            $bindings
        );

        return [$lists, $count];
    }
}
