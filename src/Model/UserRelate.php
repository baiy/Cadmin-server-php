<?php

namespace Baiy\Cadmin\Model;

class UserRelate extends Base
{


    public function groupIds($id): array
    {
        return $this->db->select($this->table, 'admin_user_group_id', [
            'admin_user_id' => $id
        ]);
    }

    public function userIds($id): array
    {
        return $this->db->select($this->table, 'admin_user_id', [
            'admin_user_group_id' => $id
        ]);
    }
}
