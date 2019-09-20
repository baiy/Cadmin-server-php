<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class Menu extends Base
{
    use Instance;
    use Table;

    public function getByIds($ids)
    {
        return $ids ? $this->db->select(self::table(), '*', ['id' => $ids]) : [];
    }

    public function all()
    {
        return $this->db->select(self::table(), '*', ['ORDER' => ['sort' => 'ASC', 'id' => 'ASC']]);
    }

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(MenuRelate::table(), ['admin_menu_id' => $id]);
    }
}
