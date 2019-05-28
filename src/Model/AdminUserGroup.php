<?php

namespace Baiy\Admin\Model;

use Baiy\Admin\InstanceTrait;

class AdminUserGroup extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getUids($id)
    {
        return array_column(
            $this->adapter->select("select `admin_user_id` from ".self::table()." where `admin_group_id`=?", [$id]),
            'admin_user_id'
        );
    }
}