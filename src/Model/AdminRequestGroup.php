<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminRequestGroup extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getGroups($id)
    {
        return $this->db->select(AdminGroup::table(), '*', [
                'id' => $this->db->select(self::table(), 'admin_group_id', ['admin_request_id' => $id])
            ]
        );
    }
}
