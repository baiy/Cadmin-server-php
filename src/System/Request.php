<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Dispatch\Dispatch;
use Exception;

class Request extends Base
{
    public function lists($keyword = "", $type = ""): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['OR'] = ['name[~]' => $keyword, 'action[~]' => $keyword, 'call[~]' => $keyword];
        }
        if (!empty($type)) {
            $where['type'] = $type;
        }

        list($lists, $total) = $this->page($this->model->request()->table, $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['auth'] = $this->model->auth()->getByIds(
                    $this->model->requestRelate()->authIds($item['id'])
                );
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $action, $type, $call, $id = ""): void
    {
        if (empty($name) || empty($action) || empty($call) || empty($type)) {
            throw new Exception("参数错误");
        }

        if ($id) {
            $this->db->update(
                $this->model->request()->table,
                compact('name', 'action', 'type', 'call'),
                compact('id')
            );
        } else {
            $this->db->insert($this->model->request()->table, compact('name', 'action', 'type', 'call'));
        }
    }

    public function remove($id): void
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        $this->model->request()->delete($id);
    }

    public function type(): array
    {
        return array_values(array_map(function (Dispatch $dispatcher) {
            return [
                'type'        => $dispatcher->key(),
                'name'        => $dispatcher->name(),
                'description' => $dispatcher->description(),
            ];
        }, array_reverse($this->container->admin->allDispatcher())));
    }
}
