<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Model\AdminRequest;
use Baiy\Cadmin\Model\AdminRequestRelate;
use Baiy\Cadmin\Model\AdminUserRelate;
use Exception;

class Request extends Base
{
    public function lists($keyword = "", $type = "")
    {
        $where = [];
        if (!empty($keyword)) {
            $where['OR'] = ['name[~]' => $keyword, 'action[~]' => $keyword, 'call[~]' => $keyword];
        }
        if (!empty($type)) {
            $where['type'] = $type;
        }

        list($lists, $total) = $this->page(AdminRequest::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($request) {
                $request['groups'] = array_map(function ($group) {
                    $group['uids'] = AdminUserRelate::instance()->getUids($group['id']);
                    return $group;
                }, AdminRequestRelate::instance()->getRelates($request['id']));
                return $request;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $action, $type, $call, $id = "")
    {
        if (empty($name) || empty($action) || empty($call)) {
            throw new Exception("参数错误");
        }
        if (!in_array($type, array_column(AdminRequest::TYPE_LISTS, 'v'))) {
            throw new Exception("类型错误");
        }

        if ($id) {
            $this->db->update(
                AdminRequest::table(),
                compact('name', 'action', 'type', 'call'),
                compact('id')
            );
        } else {
            $this->db->insert(AdminRequest::table(), compact('name', 'action', 'type', 'call'));
            $id = $this->db->id();
        }
        return $id;
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AdminRequest::instance()->delete($id);
        return true;
    }
}
