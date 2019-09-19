<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminUserRelate extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getUids($id)
    {
        return $this->db->select(self::table(), 'admin_user_id', [
            'admin_auth_id' => $id
        ]);
    }

    public function groupIds($id)
    {
        return $this->db->select(self::table(), 'admin_user_group_id', [
            'admin_user_id' => $id
        ]) ?: [];
    }
}