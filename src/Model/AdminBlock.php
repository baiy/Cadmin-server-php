<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminBlock extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(AdminBlockRelate::table(), ['admin_block_id' => $id]);
    }
}
