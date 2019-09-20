<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class Block extends Base
{
    use Instance;
    use Table;

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(BlockRelate::table(), ['admin_block_id' => $id]);
    }
}
