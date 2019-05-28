<?php

namespace Baiy\Admin\Model;

use Baiy\Admin\InstanceTrait;

class AdminRequestGroup extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function getGroups($id)
    {
        return $this->adapter->select(
            "select * from ".AdminGroup::table()." where  id in(
                select `admin_group_id` from ".self::table()." where `admin_request_id`=?
            )",
            [$id]
        );
    }
}
