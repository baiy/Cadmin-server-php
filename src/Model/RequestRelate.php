<?php

namespace Baiy\Cadmin\Model;

class RequestRelate extends Relate
{
    protected string $mainField = "admin_auth_id";
    protected string $relateField = "admin_request_id";

    public function init(): void
    {
        $this->relateTable = $this->model->request()->table;
    }

    public function authIds($id): array
    {
        return $this->db->select($this->table, 'admin_auth_id', [
            'admin_request_id' => $id
        ]) ?: [];
    }

    public function requestIds($id): array
    {
        return $this->db->select($this->table, 'admin_request_id', [
            'admin_auth_id' => $id
        ]) ?: [];
    }
}
