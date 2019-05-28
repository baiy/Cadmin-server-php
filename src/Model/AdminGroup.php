<?php

namespace Baiy\Admin\Model;

use Baiy\Admin\InstanceTrait;

class AdminGroup extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function delete($id)
    {
        $this->adapter->delete("delete from ".self::table()." where `id` = ?", [$id]);
        $this->adapter->delete("delete from ".AdminUserGroup::table()." where `admin_group_id` = ?", [$id]);
        $this->adapter->delete("delete from ".AdminMenuGroup::table()." where `admin_group_id` = ?", [$id]);
        $this->adapter->delete("delete from ".AdminRequestGroup::table()." where `admin_group_id` = ?", [$id]);
    }
}
