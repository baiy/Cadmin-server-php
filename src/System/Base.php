<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Context;
use Baiy\Cadmin\Db;

class Base
{
    /** @var Db */
    public $db;
    /** @var Context */
    public $context;

    public function __construct($adminContext)
    {
        $this->context = $adminContext;
        $this->db      = $this->context->getDb();
    }

    public function page($table, $where = [], $order = "")
    {
        $where    = $where ? ['AND' => $where] : [];
        $offset   = max(0, intval($this->context->getRequest()->input('offset', 0)));
        $pageSize = max(1, min(200, intval($this->context->getRequest()->input('pageSize', 20))));

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
