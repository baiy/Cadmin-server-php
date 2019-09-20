<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class RequestRelate extends Base
{
    use Instance;
    use Table;

    public function authIds($id)
    {
        return $this->db->select(self::table(), 'admin_auth_id', [
            'admin_request_id' => $id
        ]) ?: [];
    }

    public function requestIds($id)
    {
        return $this->db->select(self::table(), 'admin_request_id', [
            'admin_auth_id' => $id
        ]) ?: [];
    }
}
