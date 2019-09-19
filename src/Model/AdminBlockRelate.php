<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminBlockRelate extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function authIds($id)
    {
        return $this->db->select(self::table(), 'admin_auth_id', [
            'admin_block_id' => $id
        ]) ?: [];
    }
}
