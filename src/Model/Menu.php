<?php

namespace Baiy\Cadmin\Model;

class Menu extends Base
{
    public function all(): array
    {
        return $this->db->select($this->table, '*', ['ORDER' => ['sort' => 'ASC', 'id' => 'ASC']]);
    }
}
