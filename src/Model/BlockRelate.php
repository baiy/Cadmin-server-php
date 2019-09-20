<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class BlockRelate extends Base
{
    use Instance;
    use Table;

    public function authIds($id)
    {
        return $this->db->select(self::table(), 'admin_auth_id', [
            'admin_block_id' => $id
        ]) ?: [];
    }
}
