<?php

namespace Baiy\Cadmin\Model;

class UserRelate extends Relate
{
    protected string $mainField = "admin_user_group_id";
    protected string $relateField = "admin_user_id";

    public function init(): void
    {
        $this->relateTable = $this->model->user()->table;
    }

    public function groupIds($id): array
    {
        return $this->db->select($this->table, 'admin_user_group_id', [
            'admin_user_id' => $id
        ]);
    }

    public function userIds($id): array
    {
        return $this->db->select($this->table, 'admin_user_id', [
            'admin_user_group_id' => $id
        ]);
    }
}
