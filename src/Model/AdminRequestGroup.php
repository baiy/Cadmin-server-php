<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminRequestGroup extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getGroups($requestId)
    {
        $ids = $this->db->select(self::table(), 'admin_group_id', ['admin_request_id' => $requestId]);
        if (empty($ids)) {
            return [];
        }
        return $this->db->select(AdminGroup::table(), '*', [
                'id' => $ids
            ]
        );
    }
}
