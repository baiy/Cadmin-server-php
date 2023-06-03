<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Context;
use Baiy\Cadmin\Db;
use Baiy\Cadmin\Container;
use Baiy\Cadmin\Request;
use Baiy\Cadmin\Model;

class Base
{
    protected Db $db;
    protected Model $model;
    protected Context $context;
    protected Container $container;
    protected Request $request;

    public function __construct(Context $context)
    {
        $this->context   = $context;
        $this->container = $this->context->getContainer();
        $this->db        = $this->container->db;
        $this->model     = $this->container->model;
        $this->request   = $this->container->request;
    }

    public function page($table, $where = [], $order = ""): array
    {
        $where    = $where ? ['AND' => $where] : [];
        $offset   = max(0, intval($this->request->input('offset', 0)));
        $pageSize = max(1, min(200, intval($this->request->input('pageSize', 20))));

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
