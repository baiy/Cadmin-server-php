<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;

class AdminMenu extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function all()
    {
        return $this->db->select(self::table(), '*', ['ORDER' => ['sort' => 'ASC', 'id' => 'ASC']]);
    }

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(AdminMenuRelate::table(), ['admin_menu_id' => $id]);
    }
}
