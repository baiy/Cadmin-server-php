<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminRequestRelate extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getRelates($requestId)
    {
        $ids = $this->db->select(self::table(), 'admin_auth_id', ['admin_request_id' => $requestId]);
        if (empty($ids)) {
            return [];
        }
        return $this->db->select(AdminAuth::table(), '*', [
                'id' => $ids
            ]
        );
    }

    public function authIds($id)
    {
        return $this->db->select(self::table(), 'admin_auth_id', [
            'admin_request_id' => $id
        ]) ?: [];
    }
}
