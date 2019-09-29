<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Dispatch\Dispatch;
use Baiy\Cadmin\Dispatch\Dispatcher;
use Baiy\Cadmin\Instance;

class Request extends Base
{
    use Instance;
    use Table;

    public function getByIds($ids)
    {
        return $ids ? $this->db->select(self::table(), '*', ['id' => $ids]) : [];
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
