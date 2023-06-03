<?php

namespace Baiy\Cadmin\System;

use Exception;

class Menu extends Base
{
    public function lists(): array
    {
        return $this->model->menu()->all();
    }

    public function sort($menus): void
    {
        foreach ($menus as $menu) {
            $this->db->update(
                $this->model->menu()->table,
                ['sort' => $menu['sort']],
                ['id' => $menu['id']]
            );
        }
    }

    public function save($parent_id, $name, $url = "", $icon = "", $description = "", $admin_menu_group_id = "0", $id = 0): void
    {
        if (empty($name)) {
            throw new Exception("菜单名称不能为空");
        }
        if (!empty($parent_id)) {
            $parent = $this->db->get($this->model->menu()->table, "*", ['id' => $parent_id]);
            if (empty($parent)) {
                throw new Exception("父菜单不存在");
            }
            if (!empty($parent['url'])) {
                throw new Exception("父菜单不是目录类型菜单");
            }
        }
        $admin_menu_group_id = $url ? $admin_menu_group_id : 0;
        if ($id) {
            $this->db->update(
                $this->model->menu()->table,
                compact('name', 'parent_id', 'url', 'icon', 'description', 'admin_menu_group_id'),
                compact('id')
            );
        } else {
            // 计算排序值
            $sort = $this->db->get(
                $this->model->menu()->table, 'sort', ['AND' => compact('parent_id'), 'ORDER' => ['sort' => 'DESC']]
            );
            $sort = $sort ? $sort + 1 : 0;
            $this->db->insert($this->model->menu()->table, compact('name', 'parent_id', 'url', 'icon', 'description', 'sort', 'admin_menu_group_id'));
        }
    }

    public function remove($id): void
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        $this->model->menu()->delete($id);
    }
}
