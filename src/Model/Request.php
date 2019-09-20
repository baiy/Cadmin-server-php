<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Dispatch\Dispatch;
use Baiy\Cadmin\Dispatch\Local;
use Baiy\Cadmin\Instance;

class Request extends Base
{
    use Instance;
    use Table;
    const TYPE_LISTS = [
        1 => ['v' => 1, 'n' => '本地调用', 'dispatch' => Local::class],
    ];

    public function getByIds($ids)
    {
        return $ids ? $this->db->select(self::table(), '*', ['id' => $ids]) : [];
    }

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
        return $this->db->get(self::table(), '*', ['action' => $action]);
    }

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(RequestRelate::table(), ['admin_request_id' => $id]);
    }
}
