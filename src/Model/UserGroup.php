<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class UserGroup extends Base
{
    use Instance;
    use Table;

    public function getByIds($ids)
    {
        return $ids ? $this->db->select(self::table(), '*', ['id' => $ids]) : [];
    }

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(UserRelate::table(), ['admin_user_group_id' => $id]);
        $this->db->delete(UserGroupRelate::table(), ['admin_user_group_id' => $id]);
    }
}