<?php

namespace Baiy\Cadmin\Model;

class MenuGroup extends Base
{
    public function all(): array
    {
        return $this->db->select($this->table, '*', ['ORDER' => ['id' => 'ASC']]);
    }
}
