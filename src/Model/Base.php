<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Container;
use Baiy\Cadmin\Db;
use Baiy\Cadmin\Model;
use Baiy\Cadmin\Helper;

abstract class Base
{
    protected Db $db;
    protected Model $model;
    protected Container $container;
    public string $table;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->db        = $this->container->db;
        $this->model     = $this->container->model;
        $this->table     = $this->container->admin->getTablePrefix().Helper::parseTableName(static::class);
    }

    public function getByIds($ids): array
    {
        return $ids ? $this->db->select($this->table, '*', ['id' => $ids]) : [];
    }

    public function delete($id): bool
    {
        return !!$this->db->delete($this->table, ['id' => $id]);
    }

    public function getById($id): array
    {
        return $this->db->get($this->table, '*', ['id' => $id]) ?: [];
    }
}
