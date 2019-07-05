<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminGroup extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(AdminUserGroup::table(), ['admin_group_id' => $id]);
        $this->db->delete(AdminMenuGroup::table(), ['admin_group_id' => $id]);
        $this->db->delete(AdminRequestGroup::table(), ['admin_group_id' => $id]);
    }
}
