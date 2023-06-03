<?php

namespace Baiy\Cadmin\Model;

class Request extends Base
{
    public function getByAction($action)
    {
        return $this->db->get($this->table, '*', ['action' => $action]);
    }
}
