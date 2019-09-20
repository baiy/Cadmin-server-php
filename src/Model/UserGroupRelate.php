<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class UserGroupRelate extends Base
{
    use Instance;
    use Table;

    public function check($userGroupIds, $authIds): bool
    {
        if (empty($userGroupIds) || empty($authIds)) {
            return false;
        }
        $existAuthIds = $this->db->select(self::table(), 'admin_auth_id', [
            'admin_user_group_id' => $userGroupIds
        ]);
        if (empty($existAuthIds)) {
            return false;
        }

        return !empty(array_intersect($existAuthIds, $authIds));
    }

    public function authIds($id)
    {
        return $this->db->select(self::table(), 'admin_auth_id', [
            'admin_user_group_id' => $id
        ]) ?: [];
    }
}