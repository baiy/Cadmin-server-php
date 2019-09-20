<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Admin;
use Baiy\Cadmin\Db;

class Base
{
    /** @var Db */
    public $db;

    public function __construct()
    {
        $this->db = Admin::instance()->getAdapter()->db();
    }

    public function page($table, $where = [], $order = "")
    {
        $where = $where ? ['AND' => $where] : [];
        $offset = max(0, intval(Admin::instance()->getAdapter()->request->input('offset', 0)));
        $pageSize = max(1, min(200, intval(Admin::instance()->getAdapter()->request->input('pageSize', 20))));

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
