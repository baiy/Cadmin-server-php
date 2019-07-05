<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminUserGroup extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getUids($id)
    {
        return $this->db->select(self::table(), 'admin_user_id', [
            'admin_group_id' => $id
        ]);
    }
}