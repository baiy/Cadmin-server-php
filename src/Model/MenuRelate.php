<?php

namespace Baiy\Cadmin\Model;

class MenuRelate extends Relate
{
    protected string $mainField = "admin_auth_id";
    protected string $relateField = "admin_menu_id";

    public function init(): void
    {
        $this->relateTable = $this->model->menu()->table;
    }

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
