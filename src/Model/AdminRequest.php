<?php

namespace Baiy\Admin\Model;

use Baiy\Admin\Dispatch\Dispatch;
use Baiy\Admin\Dispatch\Local;
use Baiy\Admin\InstanceTrait;

class AdminRequest extends Base
{
    use InstanceTrait;
    use TableTrait;
    const TYPE_LISTS = [
        1 => ['v' => 1, 'n' => '本地调用', 'dispatch' => Local::class],
    ];

    /**
     * 获取调度对象
     * @param int $type
     * @return Dispatch|null
     */
    public static function getDispatch(int $type)
    {
        $lists = self::TYPE_LISTS;
        if (!isset($lists[$type])) {
            return null;
        }
        return new $lists[$type]['dispatch'];
    }

    public function getByAction($action)
    {
        return $this->adapter->selectOne("select * from ".self::table()." where `action`=? limit 1", [$action]);
    }

    public function delete($id)
    {
        $this->adapter->delete("delete from ".self::table()." where `id` = ?", [$id]);
        $this->adapter->delete("delete from ".AdminRequestGroup::table()." where `admin_request_id` = ?", [$id]);
    }
}
