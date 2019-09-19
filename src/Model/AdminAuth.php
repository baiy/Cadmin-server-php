<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminAuth extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getByIds($ids)
    {
        return $ids ? $this->db->select(self::table(), '*', ['id' => $ids]) : [];
    }

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(AdminUserRelate::table(), ['admin_auth_id' => $id]);
        $this->db->delete(AdminMenuRelate::table(), ['admin_auth_id' => $id]);
        $this->db->delete(AdminRequestRelate::table(), ['admin_auth_id' => $id]);
        $this->db->delete(AdminUserAuth::table(), ['admin_auth_id' => $id]);
    }
}
