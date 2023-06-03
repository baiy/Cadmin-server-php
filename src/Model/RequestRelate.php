<?php

namespace Baiy\Cadmin\Model;

class RequestRelate extends Base
{
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
