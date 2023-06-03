<?php

namespace Baiy\Cadmin\Model;

class MenuRelate extends Base
{
    public function authIds($id): array
    {
        return $this->db->select($this->table, 'admin_auth_id', [
            'admin_menu_id' => $id
        ]) ?: [];
    }

    public function menuIds($id): array
    {
        return $this->db->select($this->table, 'admin_menu_id', [
            'admin_auth_id' => $id
        ]) ?: [];
    }
}
