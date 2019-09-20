<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class MenuRelate extends Base
{
    use Instance;
    use Table;


    public function authIds($id)
    {
        return $this->db->select(self::table(), 'admin_auth_id', [
            'admin_menu_id' => $id
        ]) ?: [];
    }

    public function menuIds($id)
    {
        return $this->db->select(self::table(), 'admin_menu_id', [
            'admin_auth_id' => $id
        ]) ?: [];
    }
}
