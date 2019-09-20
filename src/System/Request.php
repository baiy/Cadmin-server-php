<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Model\Auth;
use Baiy\Cadmin\Model\Request as RequestModel;
use Baiy\Cadmin\Model\RequestRelate;
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

        list($lists, $total) = $this->page(RequestModel::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['auth'] = Auth::instance()->getByIds(
                    RequestRelate::instance()->authIds($item['id'])
                );
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $action, $type, $call, $id = "")
    {
        if (empty($name) || empty($action) || empty($call)) {
            throw new Exception("参数错误");
        }
        if (!in_array($type, array_column(RequestModel::TYPE_LISTS, 'v'))) {
            throw new Exception("类型错误");
        }

        if ($id) {
            $this->db->update(
                RequestModel::table(),
                compact('name', 'action', 'type', 'call'),
                compact('id')
            );
        } else {
            $this->db->insert(RequestModel::table(), compact('name', 'action', 'type', 'call'));
        }
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        RequestModel::instance()->delete($id);
    }
}
