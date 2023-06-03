<?php

namespace Baiy\Cadmin\Model;

class UserGroupRelate extends Base
{


    public function check($userGroupIds, $authIds): bool
    {
        if (empty($userGroupIds) || empty($authIds)) {
            return false;
        }
        $existAuthIds = $this->db->select($this->table, 'admin_auth_id', [
            'admin_user_group_id' => $userGroupIds
        ]);
        if (empty($existAuthIds)) {
            return false;
        }

        return !empty(array_intersect($existAuthIds, $authIds));
    }

    public function authIds($id): array
    {
        return $this->db->select($this->table, 'admin_auth_id', [
            'admin_user_group_id' => $id
        ]);
    }

    public function userGroupIds($id): array
    {
        return $this->db->select($this->table, 'admin_user_group_id', [
            'admin_auth_id' => $id
        ]);
    }
}
