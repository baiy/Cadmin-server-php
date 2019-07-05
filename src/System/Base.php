<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Handle;
use Medoo\Medoo;

class Base
{
    protected $adapter;
    /** @var Medoo */
    public $db;

    public function __construct()
    {
        $this->adapter = Handle::instance()->getAdapter();
        $this->db = $this->adapter->db();
    }

    public function page($table, $where = [], $order = "")
    {
        $where = $where ? ['AND' => $where] : [];
        $offset = max(0, intval($this->adapter->input('offset', 1)));
        $pageSize = max(1, min(200, intval($this->adapter->input('pageSize', 20))));

        $lists = $this->db->select(
            $table, '*',
            array_merge($where, [
                'ORDER' => $order,
                'LIMIT' => [$offset, $pageSize]
            ])
        );
        $count = $this->db->count($table, $where);
        return [$lists, $count];
    }
}
