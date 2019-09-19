<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminUserAuth extends Base
{
    use InstanceTrait;
    use TableTrait;

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
}