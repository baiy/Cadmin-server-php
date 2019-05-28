<?php

namespace Baiy\Admin\Model;

use Baiy\Admin\InstanceTrait;

class AdminMenu extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getById($id)
    {
        return $this->adapter->selectOne("select * from ".self::table()." where id=? limit 1", [$id]);
    }

    public function delete($id)
    {
        $this->adapter->delete("delete from ".self::table()." where `id` = ?", [$id]);
        $this->adapter->delete("delete from ".AdminMenuGroup::table()." where `admin_menu_id` = ?", [$id]);
    }
}
