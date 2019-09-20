<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class UserRelate extends Base
{
    use Instance;
    use Table;

    public function groupIds($id)
    {
        return $this->db->select(self::table(), 'admin_user_group_id', [
            'admin_user_id' => $id
        ]) ?: [];
    }
}