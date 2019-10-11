<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Admin;
use Baiy\Cadmin\Dispatch\Dispatch;
use Baiy\Cadmin\Model\Auth;
use Baiy\Cadmin\Model\Request as RequestModel;
use Baiy\Cadmin\Model\RequestRelate;
use Exception;
use Parsedown;

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
        if (empty($name) || empty($action) || empty($call) || empty($type)) {
            throw new Exception("参数错误");
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

    public function type()
    {
        return array_values(array_map(function (Dispatch $dispatcher) {
            return [
                'type'        => $dispatcher->key(),
                'name'        => $dispatcher->name(),
                'description' => (new Parsedown())->text($dispatcher->description()),
            ];
        }, Admin::instance()->allDispatcher()));
    }
}
