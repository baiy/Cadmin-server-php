<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Model\AdminAuth;
use Baiy\Cadmin\Model\AdminBlock;
use Baiy\Cadmin\Model\AdminBlockRelate;
use Exception;

class Block extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        if (!empty($keyword)) {
            $where['OR'] = ['name[~]' => $keyword, 'action[~]' => $keyword];
        }

        list($lists, $total) = $this->page(AdminBlock::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($block) {
                $block['auth'] = AdminAuth::instance()->getByIds(
                    AdminBlockRelate::instance()->authIds($block['id'])
                );
                return $block;
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
                AdminBlock::table(),
                compact('name', 'action', 'description'),
                compact('id')
            );
        } else {
            $this->db->insert(AdminBlock::table(), compact('name', 'action', 'description'));
            $this->db->id();
        }
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AdminBlock::instance()->delete($id);
        return true;
    }
}
