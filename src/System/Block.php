<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Model\Auth;
use Baiy\Cadmin\Model\Block as BlockModel;
use Baiy\Cadmin\Model\BlockRelate;
use Exception;

class Block extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        if (!empty($keyword)) {
            $where['OR'] = ['name[~]' => $keyword, 'action[~]' => $keyword];
        }

        list($lists, $total) = $this->page(BlockModel::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['auth'] = Auth::instance()->getByIds(
                    BlockRelate::instance()->authIds($item['id'])
                );
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $action, $description = "", $id = 0)
    {
        if (empty($name)) {
            throw new Exception("区块名称不能为空");
        }
        if (empty($action)) {
            throw new Exception("区块名称不能为空");
        }

        if ($id) {
            $this->db->update(
                BlockModel::table(),
                compact('name', 'action', 'description'),
                compact('id')
            );
        } else {
            $this->db->insert(BlockModel::table(), compact('name', 'action', 'description'));
        }
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        BlockModel::instance()->delete($id);
    }
}
